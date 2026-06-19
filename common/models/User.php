<?php

declare(strict_types=1);

namespace common\models;

use common\components\OtpService;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property int $id
 * @property string $username
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string|null $password_reset_otp_hash
 * @property int|null $password_reset_otp_expires_at
 * @property string|null $verification_token
 * @property string|null $verification_otp_hash
 * @property int|null $verification_otp_expires_at
 * @property string $email
 * @property string $phone
 * @property string $auth_key
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string $role
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    public const STATUS_DELETED = 0;
    public const STATUS_INACTIVE = 9;
    public const STATUS_ACTIVE = 10;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_CUSTOMER = 'customer';

    public static function tableName(): string
    {
        return '{{%user}}';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules(): array
    {
        return [
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
            ['role', 'default', 'value' => self::ROLE_CUSTOMER],
            ['role', 'in', 'range' => [self::ROLE_ADMIN, self::ROLE_CUSTOMER]],
            [['username', 'email', 'phone'], 'string', 'max' => 255],
            ['email', 'email'],
            ['phone', 'match', 'pattern' => '/^\+?[0-9\s\-()]{7,20}$/', 'message' => 'Please enter a valid phone number.'],
            ['email', 'unique', 'filter' => ['!=', 'status', self::STATUS_DELETED]],
            ['username', 'unique', 'filter' => ['!=', 'status', self::STATUS_DELETED]],
        ];
    }

    public static function findIdentity($id): User|null
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token, $type = null): never
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public static function findByUsername(string $username): User|null
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findByUsernameAnyStatus(string $username): User|null
    {
        return static::find()
            ->where(['username' => $username])
            ->andWhere(['!=', 'status', self::STATUS_DELETED])
            ->one();
    }

    public static function findByEmail(string $email): User|null
    {
        return static::find()
            ->where(['email' => $email])
            ->andWhere(['!=', 'status', self::STATUS_DELETED])
            ->one();
    }

    public static function findInactiveByEmail(string $email): User|null
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_INACTIVE]);
    }

    public static function findActiveByEmail(string $email): User|null
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findActiveByPhone(string $phone): User|null
    {
        $normalized = preg_replace('/[\s\-()]/', '', $phone);

        return static::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->andWhere([
                'or',
                ['phone' => $phone],
                ['phone' => $normalized],
                ['like', 'phone', $normalized, false],
            ])
            ->one();
    }

    /**
     * Finds an active user by email address or phone number.
     */
    public static function findActiveByIdentifier(string $identifier): User|null
    {
        $identifier = trim($identifier);
        if ($identifier === '') {
            return null;
        }

        if (str_contains($identifier, '@')) {
            return static::findActiveByEmail($identifier);
        }

        return static::findActiveByPhone($identifier);
    }

    public static function findByPasswordResetToken(string $token): User|null
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    public static function findByVerificationToken(string $token): User|null
    {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE,
        ]);
    }

    public static function isPasswordResetTokenValid(string|null $token): bool
    {
        if ($token === null || $token === '') {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];

        return $timestamp + $expire >= time();
    }

    public function getId(): int
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword(string $password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generatePasswordResetToken(): void
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function generateEmailVerificationToken(): void
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * @return string The plain OTP code (send via email only).
     */
    public function generateVerificationOtp(): string
    {
        $otpService = new OtpService();
        $code = $otpService->generateCode();
        $this->verification_otp_hash = $otpService->hashCode($code);
        $this->verification_otp_expires_at = $otpService->getExpiryTimestamp();

        return $code;
    }

    /**
     * @return string The plain OTP code (send via email only).
     */
    public function generatePasswordResetOtp(): string
    {
        $otpService = new OtpService();
        $code = $otpService->generateCode();
        $this->password_reset_otp_hash = $otpService->hashCode($code);
        $this->password_reset_otp_expires_at = $otpService->getExpiryTimestamp();

        return $code;
    }

    public function validateVerificationOtp(string $code): bool
    {
        $otpService = new OtpService();

        if ($otpService->isExpired($this->verification_otp_expires_at)) {
            return false;
        }

        return $otpService->validateCode($code, $this->verification_otp_hash);
    }

    public function validatePasswordResetOtp(string $code): bool
    {
        $otpService = new OtpService();

        if ($otpService->isExpired($this->password_reset_otp_expires_at)) {
            return false;
        }

        return $otpService->validateCode($code, $this->password_reset_otp_hash);
    }

    public function activateAccount(): bool
    {
        $this->status = self::STATUS_ACTIVE;
        $this->verification_otp_hash = null;
        $this->verification_otp_expires_at = null;
        $this->verification_token = null;

        return $this->save(false);
    }

    public function removePasswordResetToken(): void
    {
        $this->password_reset_token = null;
    }

    public function clearPasswordResetOtp(): void
    {
        $this->password_reset_otp_hash = null;
        $this->password_reset_otp_expires_at = null;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }
}

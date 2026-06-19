<?php

declare(strict_types=1);

namespace frontend\models;

use common\models\User;
use common\validators\StrongPasswordValidator;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;

/**
 * Set a new password after reset link or code verification.
 */
class ResetPasswordForm extends Model
{
    public string $password = '';
    public string $passwordConfirm = '';
    private User|null $_user = null;

    /**
     * @param string|null $token Password reset token from email link.
     */
    public function __construct(string|null $token = null, array $config = [])
    {
        if ($token !== null && $token !== '') {
            $this->_user = User::findByPasswordResetToken($token);
            if ($this->_user === null) {
                throw new InvalidArgumentException('This password reset link is invalid or has expired.');
            }
        }

        parent::__construct($config);
    }

    public function init(): void
    {
        parent::init();

        if ($this->_user === null) {
            $userId = Yii::$app->session->get('passwordResetVerifiedUserId');
            if ($userId !== null) {
                $this->_user = User::findOne(['id' => $userId, 'status' => User::STATUS_ACTIVE]);
            }
        }
    }

    public function rules(): array
    {
        return [
            [['password', 'passwordConfirm'], 'required'],
            ['password', StrongPasswordValidator::class, 'minLength' => (int) Yii::$app->params['user.passwordMinLength']],
            ['passwordConfirm', 'compare', 'compareAttribute' => 'password', 'message' => 'Passwords do not match.'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'password' => 'New Password',
            'passwordConfirm' => 'Confirm Password',
        ];
    }

    public function isAuthorized(): bool
    {
        return $this->_user !== null;
    }

    public function resetPassword(): bool
    {
        if (!$this->isAuthorized() || !$this->validate()) {
            return false;
        }

        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();
        $user->clearPasswordResetOtp();
        $user->generateAuthKey();

        if (!$user->save(false)) {
            return false;
        }

        Yii::$app->session->remove('passwordResetVerifiedUserId');
        Yii::$app->session->remove('passwordResetIdentifier');

        return true;
    }
}

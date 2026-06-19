<?php

declare(strict_types=1);

namespace frontend\models;

use common\models\User;
use Yii;
use yii\base\Model;

/**
 * Password reset code verification (alternative to reset link).
 */
class PasswordResetVerifyForm extends Model
{
    public string $identifier = '';
    public string $otp = '';

    public function rules(): array
    {
        return [
            [['identifier', 'otp'], 'required'],
            ['identifier', 'trim'],
            ['otp', 'trim'],
            ['otp', 'match', 'pattern' => '/^\d{6}$/', 'message' => 'Reset code must be a 6-digit number.'],
            ['otp', 'validateOtp'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'identifier' => 'Email or Phone Number',
            'otp' => 'Reset Code',
        ];
    }

    public function validateOtp(string $attribute): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $user = User::findActiveByIdentifier($this->identifier);

        if ($user === null) {
            $this->addError('identifier', 'No account found with that email or phone number.');

            return;
        }

        if ($user->password_reset_otp_expires_at !== null && $user->password_reset_otp_expires_at < time()) {
            $this->addError($attribute, 'This reset code has expired. Please request a new one.');

            return;
        }

        if (!$user->validatePasswordResetOtp($this->otp)) {
            $this->addError($attribute, 'Invalid reset code. Please check and try again.');
        }
    }

    public function verify(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $user = User::findActiveByIdentifier($this->identifier);

        if ($user === null) {
            return false;
        }

        Yii::$app->session->set('passwordResetVerifiedUserId', $user->id);

        return true;
    }
}

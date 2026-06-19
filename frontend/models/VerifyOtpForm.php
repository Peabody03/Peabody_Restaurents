<?php

declare(strict_types=1);

namespace frontend\models;

use common\models\User;
use Yii;
use yii\base\Model;

/**
 * Email verification OTP form
 */
class VerifyOtpForm extends Model
{
    public string $email = '';
    public string $otp = '';

    public function rules(): array
    {
        return [
            [['email', 'otp'], 'required'],
            ['email', 'trim'],
            ['email', 'email'],
            ['otp', 'trim'],
            ['otp', 'match', 'pattern' => '/^\d{6}$/', 'message' => 'Verification code must be a 6-digit number.'],
            ['otp', 'validateOtp'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'email' => 'Email Address',
            'otp' => 'Verification Code',
        ];
    }

    public function validateOtp(string $attribute): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $user = User::findInactiveByEmail($this->email);

        if ($user === null) {
            $this->addError('email', 'No pending verification found for this email address.');

            return;
        }

        if ($user->verification_otp_expires_at !== null && $user->verification_otp_expires_at < time()) {
            $this->addError($attribute, 'This verification code has expired. Please request a new one.');

            return;
        }

        if (!$user->validateVerificationOtp($this->otp)) {
            $this->addError($attribute, 'Invalid verification code. Please try again.');
        }
    }

    public function verify(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $user = User::findInactiveByEmail($this->email);

        return $user !== null && $user->activateAccount();
    }
}

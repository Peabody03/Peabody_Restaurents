<?php

declare(strict_types=1);

namespace frontend\models;

use common\models\User;
use Yii;
use yii\base\Model;
use yii\mail\MailerInterface;

/**
 * Resend email verification OTP form
 */
class ResendVerificationOtpForm extends Model
{
    public string $email = '';

    public function rules(): array
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => User::class,
                'filter' => ['status' => User::STATUS_INACTIVE],
                'message' => 'There is no pending verification for this email address.',
            ],
        ];
    }

    public function sendEmail(MailerInterface $mailer, string $supportEmail, string $appName): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $user = User::findInactiveByEmail($this->email);

        if ($user === null) {
            return false;
        }

        $otpCode = $user->generateVerificationOtp();

        if (!$user->save(false)) {
            return false;
        }

        $expireMinutes = (int) (Yii::$app->params['user.otpExpire'] / 60);

        return $mailer
            ->compose(
                ['html' => 'otpVerification-html', 'text' => 'otpVerification-text'],
                [
                    'user' => $user,
                    'otpCode' => $otpCode,
                    'expireMinutes' => $expireMinutes,
                    'purpose' => 'account verification',
                ],
            )
            ->setFrom([$supportEmail => $appName])
            ->setTo($user->email)
            ->setSubject('Your new verification code - ' . $appName)
            ->send();
    }
}

<?php

declare(strict_types=1);

namespace frontend\models;

use common\models\User;
use common\services\SmsService;
use Yii;
use yii\base\Model;
use yii\mail\MailerInterface;

/**
 * Password reset request — accepts registered email or phone number.
 */
class PasswordResetRequestForm extends Model
{
    public string $identifier = '';
    private User|null $_user = null;

    public function rules(): array
    {
        return [
            ['identifier', 'trim'],
            ['identifier', 'required'],
            ['identifier', 'validateIdentifier'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'identifier' => 'Email or Phone Number',
        ];
    }

    public function validateIdentifier(string $attribute): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $this->_user = User::findActiveByIdentifier($this->identifier);

        if ($this->_user === null) {
            $this->addError($attribute, 'No account found with that email or phone number.');
        }
    }

    public function sendReset(MailerInterface $mailer, string $supportEmail, string $appName): bool
    {
        if (!$this->validate() || $this->_user === null) {
            return false;
        }

        $user = $this->_user;
        $user->generatePasswordResetToken();
        $otpCode = $user->generatePasswordResetOtp();

        if (!$user->save(false)) {
            return false;
        }

        $resetLink = Yii::$app->urlManager->createAbsoluteUrl([
            'site/reset-password',
            'token' => $user->password_reset_token,
        ]);
        $expireMinutes = (int) (Yii::$app->params['user.otpExpire'] / 60);

        $emailSent = $mailer
            ->compose(
                ['html' => 'passwordResetCombined-html', 'text' => 'passwordResetCombined-text'],
                [
                    'user' => $user,
                    'resetLink' => $resetLink,
                    'otpCode' => $otpCode,
                    'expireMinutes' => $expireMinutes,
                ],
            )
            ->setFrom([$supportEmail => $appName])
            ->setTo($user->email)
            ->setSubject('Reset your password - ' . $appName)
            ->send();

        $viaPhone = str_contains($this->identifier, '@') === false;
        if ($viaPhone) {
            (new SmsService())->send(
                $user->phone,
                "Your {$appName} reset code is {$otpCode}. Valid for {$expireMinutes} min. Or use the link sent to your email.",
            );
        }

        Yii::$app->session->set('passwordResetIdentifier', $this->identifier);

        return $emailSent;
    }
}

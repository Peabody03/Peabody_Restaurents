<?php

declare(strict_types=1);

namespace common\components;

use Yii;

/**
 * Generates and validates one-time passwords.
 */
class OtpService
{
    public function generateCode(): string
    {
        return sprintf('%06d', random_int(0, 999999));
    }

    public function hashCode(string $code): string
    {
        return Yii::$app->security->generatePasswordHash($code);
    }

    public function validateCode(string $code, string|null $hash): bool
    {
        if ($hash === null || $hash === '') {
            return false;
        }

        return Yii::$app->security->validatePassword($code, $hash);
    }

    public function getExpiryTimestamp(): int
    {
        return time() + (int) Yii::$app->params['user.otpExpire'];
    }

    public function isExpired(int|null $expiresAt): bool
    {
        return $expiresAt === null || $expiresAt < time();
    }
}

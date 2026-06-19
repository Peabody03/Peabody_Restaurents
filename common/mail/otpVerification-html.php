<?php

declare(strict_types=1);

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\User $user */
/** @var string $otpCode */
/** @var int $expireMinutes */
/** @var string $purpose */
?>
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2 style="color: #2c5282;">Verify Your Account</h2>
    <p>Hello <?= Html::encode($user->username) ?>,</p>
    <p>Use the verification code below to complete your <?= Html::encode($purpose) ?>:</p>
    <div style="background: #f7fafc; border: 2px dashed #4299e1; border-radius: 8px; padding: 20px; text-align: center; margin: 24px 0;">
        <span style="font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #2b6cb0;"><?= Html::encode($otpCode) ?></span>
    </div>
    <p style="color: #718096;">This code expires in <strong><?= (int) $expireMinutes ?> minutes</strong>.</p>
    <p style="color: #a0aec0; font-size: 12px;">If you did not request this code, please ignore this email.</p>
</div>

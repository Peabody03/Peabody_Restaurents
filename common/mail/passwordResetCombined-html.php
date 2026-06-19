<?php

declare(strict_types=1);

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\User $user */
/** @var string $resetLink */
/** @var string $otpCode */
/** @var int $expireMinutes */
?>
<div style="font-family: Arial, sans-serif; max-width: 560px; margin: 0 auto; padding: 24px;">
    <h2 style="color: #ea580c;">Reset Your Password</h2>
    <p>Hello <?= Html::encode($user->username) ?>,</p>
    <p>We received a request to reset your password. Choose the easiest option below:</p>

    <div style="margin: 24px 0; padding: 20px; background: #fff7ed; border-radius: 12px; text-align: center;">
        <p style="margin: 0 0 12px; font-weight: 600;">Option 1 — Click to reset instantly</p>
        <a href="<?= Html::encode($resetLink) ?>" style="display: inline-block; background: #f97316; color: #fff; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-weight: bold;">Reset My Password</a>
    </div>

    <div style="margin: 24px 0; padding: 20px; background: #f8fafc; border-radius: 12px; text-align: center;">
        <p style="margin: 0 0 8px; font-weight: 600;">Option 2 — Enter this code on the website</p>
        <span style="font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #ea580c;"><?= Html::encode($otpCode) ?></span>
    </div>

    <p style="color: #64748b; font-size: 14px;">This link and code expire in <strong><?= (int) $expireMinutes ?> minutes</strong>.</p>
    <p style="color: #94a3b8; font-size: 12px;">If you did not request a password reset, you can safely ignore this email.</p>
</div>

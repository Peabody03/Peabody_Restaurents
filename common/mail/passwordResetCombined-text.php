<?php

declare(strict_types=1);

/** @var common\models\User $user */
/** @var string $resetLink */
/** @var string $otpCode */
/** @var int $expireMinutes */
?>
Hello <?= $user->username ?>,

We received a request to reset your password.

OPTION 1 — Click this link to reset instantly:
<?= $resetLink ?>


OPTION 2 — Enter this code on the website:
<?= $otpCode ?>


This link and code expire in <?= (int) $expireMinutes ?> minutes.

If you did not request a password reset, please ignore this email.

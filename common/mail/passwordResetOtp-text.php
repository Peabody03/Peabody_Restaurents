<?php

declare(strict_types=1);

/** @var common\models\User $user */
/** @var string $otpCode */
/** @var int $expireMinutes */
?>
Hello <?= $user->username ?>,

We received a request to reset your password. Use the code below to continue:

<?= $otpCode ?>

This code expires in <?= (int) $expireMinutes ?> minutes.

If you did not request a password reset, please ignore this email.

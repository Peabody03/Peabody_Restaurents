<?php

declare(strict_types=1);

/** @var common\models\User $user */
/** @var string $otpCode */
/** @var int $expireMinutes */
/** @var string $purpose */
?>
Hello <?= $user->username ?>,

Use the verification code below to complete your <?= $purpose ?>:

<?= $otpCode ?>

This code expires in <?= (int) $expireMinutes ?> minutes.

If you did not request this code, please ignore this email.

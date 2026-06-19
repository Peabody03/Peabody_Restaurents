<?php

declare(strict_types=1);

return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Restaurants App',
    'user.passwordResetTokenExpire' => 3600,
    'user.passwordMinLength' => 8,
    'user.otpExpire' => 900,
    'user.loginMaxAttempts' => 5,
    'user.loginLockoutDuration' => 900,
    'uploads.baseUrl' => '@web/uploads', // stored in @frontend/web/uploads; shared URL resolver used in admin
    'restaurant.name' => 'Peabody Restaurant',
    'restaurant.displayName' => 'Peabody_Restaurent',
    'restaurant.taxRate' => 0.18,
    'restaurant.whatsapp' => '0627434348',
    'restaurant.whatsappIntl' => '255627434348',
];

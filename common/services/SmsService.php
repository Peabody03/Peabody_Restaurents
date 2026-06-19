<?php

declare(strict_types=1);

namespace common\services;

use Yii;

/**
 * SMS delivery stub — logs messages to file in development.
 * Replace with a real SMS gateway (Twilio, Africa's Talking, etc.) in production.
 */
class SmsService
{
    public function send(string $phone, string $message): bool
    {
        $dir = Yii::getAlias('@frontend/runtime/sms');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $file = $dir . '/sms_' . date('Y-m-d_His') . '_' . preg_replace('/[^0-9]/', '', $phone) . '.txt';
        $content = "To: {$phone}\nTime: " . date('c') . "\n\n{$message}\n";

        return file_put_contents($file, $content) !== false;
    }
}

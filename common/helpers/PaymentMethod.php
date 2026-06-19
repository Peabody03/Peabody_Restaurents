<?php

declare(strict_types=1);

namespace common\helpers;

/**
 * Supported checkout payment methods.
 */
final class PaymentMethod
{
    public const CASH = 'cash';
    public const MASTERCARD = 'mastercard';
    public const VISA = 'visa';
    public const AMEX = 'amex';
    public const MOBILE_MONEY = 'mobile_money';

    /** @deprecated Legacy value stored on older orders */
    public const CARD_LEGACY = 'card';

    /**
     * @return array<string, array{label: string, description: string, type: string, brand: string}>
     */
    public static function definitions(): array
    {
        return [
            self::MASTERCARD => [
                'label' => 'Mastercard',
                'description' => 'Debit or credit card',
                'type' => 'card',
                'brand' => 'mastercard',
            ],
            self::VISA => [
                'label' => 'Visa',
                'description' => 'Debit or credit card',
                'type' => 'card',
                'brand' => 'visa',
            ],
            self::AMEX => [
                'label' => 'American Express',
                'description' => 'Amex card',
                'type' => 'card',
                'brand' => 'amex',
            ],
            self::MOBILE_MONEY => [
                'label' => 'Mobile Money',
                'description' => 'M-Pesa, Tigo Pesa, Airtel Money',
                'type' => 'mobile',
                'brand' => 'mobile',
            ],
            self::CASH => [
                'label' => 'Cash',
                'description' => 'Pay on pickup or delivery',
                'type' => 'cash',
                'brand' => 'cash',
            ],
        ];
    }

    /**
     * @return string[]
     */
    public static function keys(): array
    {
        return array_keys(self::definitions());
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::definitions() as $key => $definition) {
            $options[$key] = $definition['label'];
        }

        return $options;
    }

    public static function isValid(?string $method): bool
    {
        if ($method === null || $method === '') {
            return false;
        }

        return isset(self::definitions()[$method]) || $method === self::CARD_LEGACY;
    }

    public static function label(?string $method): string
    {
        if ($method === null || $method === '') {
            return '—';
        }

        if ($method === self::CARD_LEGACY) {
            return 'Credit / Debit Card';
        }

        return self::definitions()[$method]['label'] ?? ucfirst(str_replace('_', ' ', $method));
    }

    public static function type(?string $method): string
    {
        if ($method === self::CARD_LEGACY) {
            return 'card';
        }

        return self::definitions()[$method]['type'] ?? 'other';
    }

    public static function brand(?string $method): string
    {
        if ($method === self::CARD_LEGACY) {
            return 'card';
        }

        return self::definitions()[$method]['brand'] ?? 'other';
    }

    public static function isCard(?string $method): bool
    {
        return self::type($method) === 'card';
    }
}

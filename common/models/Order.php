<?php

declare(strict_types=1);

namespace common\models;

use common\helpers\PaymentMethod;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $order_number
 * @property int $user_id
 * @property string $status
 * @property string|null $payment_method
 * @property string $payment_status
 * @property string $delivery_type
 * @property float $subtotal
 * @property float $tax
 * @property float $discount
 * @property float $total
 * @property string|null $notes
 * @property int $created_at
 * @property int $updated_at
 * @property User $user
 * @property OrderItem[] $items
 */
class Order extends ActiveRecord
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_PREPARING = 'preparing';
    public const STATUS_READY = 'ready';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    public static function tableName(): string
    {
        return '{{%order}}';
    }

    public function behaviors(): array
    {
        return [TimestampBehavior::class];
    }

    public function rules(): array
    {
        return [
            [['order_number', 'user_id', 'total'], 'required'],
            ['order_number', 'string', 'max' => 32],
            ['status', 'in', 'range' => array_keys(self::statuses())],
            ['payment_status', 'in', 'range' => ['unpaid', 'paid', 'refunded']],
            ['payment_method', 'in', 'range' => array_merge(PaymentMethod::keys(), [PaymentMethod::CARD_LEGACY, null]), 'skipOnEmpty' => true],
            ['delivery_type', 'in', 'range' => ['pickup', 'delivery']],
            [['subtotal', 'tax', 'discount', 'total'], 'number', 'min' => 0],
            ['notes', 'string'],
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_PREPARING => 'Preparing',
            self::STATUS_READY => 'Ready',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public function getUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getItems(): \yii\db\ActiveQuery
    {
        return $this->hasMany(OrderItem::class, ['order_id' => 'id']);
    }

    public static function generateOrderNumber(): string
    {
        return 'PB-' . strtoupper(substr(uniqid(), -8)) . '-' . random_int(100, 999);
    }

    public function getFormattedTotal(): string
    {
        return 'TZS ' . number_format((float) $this->total, 0, '.', ',');
    }

    public function getPaymentMethodLabel(): string
    {
        return PaymentMethod::label($this->payment_method);
    }

    public function getPaymentMethodBrand(): string
    {
        return PaymentMethod::brand($this->payment_method);
    }
}

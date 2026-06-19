<?php

declare(strict_types=1);

namespace frontend\models;

use common\helpers\PaymentMethod;
use yii\base\Model;

class CheckoutForm extends Model
{
    public string $payment_method = PaymentMethod::MASTERCARD;
    public string $delivery_type = 'pickup';
    public string $notes = '';

    public function rules(): array
    {
        return [
            [['payment_method', 'delivery_type'], 'required'],
            ['payment_method', 'in', 'range' => PaymentMethod::keys()],
            ['delivery_type', 'in', 'range' => ['pickup', 'delivery']],
            ['notes', 'string', 'max' => 500],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'payment_method' => 'Payment Method',
            'delivery_type' => 'Delivery Type',
            'notes' => 'Order Notes',
        ];
    }
}

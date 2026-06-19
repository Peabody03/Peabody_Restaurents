<?php

declare(strict_types=1);

namespace common\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $order_id
 * @property int $food_id
 * @property string $food_name
 * @property int $quantity
 * @property float $unit_price
 * @property float $total_price
 * @property Food $food
 */
class OrderItem extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%order_item}}';
    }

    public function rules(): array
    {
        return [
            [['order_id', 'food_id', 'food_name', 'quantity', 'unit_price', 'total_price'], 'required'],
            ['quantity', 'integer', 'min' => 1],
            [['unit_price', 'total_price'], 'number', 'min' => 0],
        ];
    }

    public function getFood(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Food::class, ['id' => 'food_id']);
    }
}

<?php

declare(strict_types=1);

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $user_id
 * @property int $food_id
 * @property int $quantity
 * @property int $created_at
 * @property int $updated_at
 * @property Food $food
 */
class CartItem extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%cart_item}}';
    }

    public function behaviors(): array
    {
        return [TimestampBehavior::class];
    }

    public function rules(): array
    {
        return [
            [['user_id', 'food_id', 'quantity'], 'required'],
            ['quantity', 'integer', 'min' => 1],
        ];
    }

    public function getFood(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Food::class, ['id' => 'food_id']);
    }

    public function getLineTotal(): float
    {
        return (float) $this->food->price * $this->quantity;
    }
}

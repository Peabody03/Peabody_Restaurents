<?php

declare(strict_types=1);

namespace common\services;

use common\models\CartItem;
use common\models\Food;
use common\models\Order;
use common\models\OrderItem;
use Yii;
use yii\db\Exception;

class CartService
{
    public function getItems(int $userId): array
    {
        return CartItem::find()
            ->where(['user_id' => $userId])
            ->with('food')
            ->all();
    }

    public function getCount(int $userId): int
    {
        return (int) CartItem::find()->where(['user_id' => $userId])->sum('quantity');
    }

    public function add(int $userId, int $foodId, int $quantity = 1): bool
    {
        $food = Food::findOne(['id' => $foodId, 'is_available' => true]);
        if ($food === null) {
            return false;
        }

        $item = CartItem::findOne(['user_id' => $userId, 'food_id' => $foodId]);
        if ($item === null) {
            $item = new CartItem(['user_id' => $userId, 'food_id' => $foodId, 'quantity' => $quantity]);
        } else {
            $item->quantity += $quantity;
        }

        return $item->save();
    }

    public function updateQuantity(int $userId, int $foodId, int $quantity): bool
    {
        if ($quantity < 1) {
            return $this->remove($userId, $foodId);
        }

        $item = CartItem::findOne(['user_id' => $userId, 'food_id' => $foodId]);

        if ($item === null) {
            return false;
        }

        $item->quantity = $quantity;

        return $item->save();
    }

    public function remove(int $userId, int $foodId): bool
    {
        $item = CartItem::findOne(['user_id' => $userId, 'food_id' => $foodId]);

        return $item !== null ? (bool) $item->delete() : true;
    }

    public function clear(int $userId): void
    {
        CartItem::deleteAll(['user_id' => $userId]);
    }

    public function getSubtotal(int $userId): float
    {
        $total = 0.0;
        foreach ($this->getItems($userId) as $item) {
            if ($item->food !== null) {
                $total += $item->getLineTotal();
            }
        }

        return $total;
    }

    /**
     * @throws Exception
     */
    public function checkout(
        int $userId,
        string $paymentMethod,
        string $deliveryType,
        string|null $notes = null,
        float $discount = 0,
    ): Order|null {
        $items = $this->getItems($userId);
        if ($items === []) {
            return null;
        }

        $subtotal = $this->getSubtotal($userId);
        $taxRate = (float) Yii::$app->params['restaurant.taxRate'];
        $tax = round($subtotal * $taxRate, 2);
        $total = max(0, $subtotal + $tax - $discount);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $order = new Order();
            $order->order_number = Order::generateOrderNumber();
            $order->user_id = $userId;
            $order->status = Order::STATUS_PENDING;
            $order->payment_method = $paymentMethod;
            $order->payment_status = 'paid';
            $order->delivery_type = $deliveryType;
            $order->subtotal = $subtotal;
            $order->tax = $tax;
            $order->discount = $discount;
            $order->total = $total;
            $order->notes = $notes;

            if (!$order->save()) {
                throw new Exception('Failed to create order.');
            }

            foreach ($items as $cartItem) {
                $food = $cartItem->food;
                if ($food === null) {
                    continue;
                }
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->food_id = $food->id;
                $orderItem->food_name = $food->food_name;
                $orderItem->quantity = $cartItem->quantity;
                $orderItem->unit_price = $food->price;
                $orderItem->total_price = $cartItem->getLineTotal();
                if (!$orderItem->save()) {
                    throw new Exception('Failed to save order item.');
                }
            }

            $this->clear($userId);
            $transaction->commit();

            return $order;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage(), __METHOD__);

            return null;
        }
    }
}

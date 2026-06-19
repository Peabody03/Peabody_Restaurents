<?php

declare(strict_types=1);

namespace backend\controllers;

use common\helpers\PaymentMethod;
use common\models\Food;
use common\models\Order;
use common\services\CartService;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class PosController extends BaseAdminController
{
    public function actionIndex(): string
    {
        $foods = Food::find()->where(['is_available' => true])->orderBy(['menu_type' => SORT_ASC, 'food_name' => SORT_ASC])->all();

        return $this->render('index', ['foods' => $foods]);
    }

    public function actionSearch(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $q = trim((string) Yii::$app->request->get('q', ''));
        $query = Food::find()->where(['is_available' => true]);
        if ($q !== '') {
            $query->andWhere(['or', ['like', 'food_name', $q], ['like', 'menu_type', $q]]);
        }

        return array_map(static fn (Food $f) => [
            'id' => $f->id,
            'name' => $f->food_name,
            'price' => (float) $f->price,
            'formattedPrice' => $f->getFormattedPrice(),
            'image' => $f->getImageUrl(),
            'category' => Food::menuTypes()[$f->menu_type] ?? $f->menu_type,
        ], $query->limit(24)->all());
    }

    public function actionCheckout(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $itemsRaw = Yii::$app->request->post('items');
        $items = is_string($itemsRaw) ? json_decode($itemsRaw, true) : $itemsRaw;
        $paymentMethod = (string) Yii::$app->request->post('payment_method', PaymentMethod::CASH);
        if (!PaymentMethod::isValid($paymentMethod)) {
            return ['success' => false, 'message' => 'Invalid payment method.'];
        }
        $discount = (float) Yii::$app->request->post('discount', 0);

        if (!is_array($items) || $items === []) {
            return ['success' => false, 'message' => 'Cart is empty.'];
        }

        $subtotal = 0.0;
        $orderItems = [];
        foreach ($items as $row) {
            $food = Food::findOne(['id' => (int) ($row['id'] ?? 0), 'is_available' => true]);
            $qty = max(1, (int) ($row['qty'] ?? 1));
            if ($food === null) {
                continue;
            }
            $line = (float) $food->price * $qty;
            $subtotal += $line;
            $orderItems[] = ['food' => $food, 'qty' => $qty, 'line' => $line];
        }

        if ($orderItems === []) {
            return ['success' => false, 'message' => 'No valid items in cart.'];
        }

        $taxRate = (float) Yii::$app->params['restaurant.taxRate'];
        $tax = round($subtotal * $taxRate, 2);
        $total = max(0, $subtotal + $tax - $discount);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $order = new Order();
            $order->order_number = Order::generateOrderNumber();
            $order->user_id = Yii::$app->user->id;
            $order->status = Order::STATUS_CONFIRMED;
            $order->payment_method = $paymentMethod;
            $order->payment_status = 'paid';
            $order->delivery_type = 'pickup';
            $order->subtotal = $subtotal;
            $order->tax = $tax;
            $order->discount = $discount;
            $order->total = $total;
            $order->notes = 'POS order';

            if (!$order->save()) {
                throw new \RuntimeException('Order save failed');
            }

            foreach ($orderItems as $row) {
                $oi = new \common\models\OrderItem();
                $oi->order_id = $order->id;
                $oi->food_id = $row['food']->id;
                $oi->food_name = $row['food']->food_name;
                $oi->quantity = $row['qty'];
                $oi->unit_price = $row['food']->price;
                $oi->total_price = $row['line'];
                $oi->save(false);
            }

            $transaction->commit();

            return [
                'success' => true,
                'orderNumber' => $order->order_number,
                'total' => $total,
                'formattedTotal' => $order->getFormattedTotal(),
            ];
        } catch (\Throwable $e) {
            $transaction->rollBack();

            return ['success' => false, 'message' => 'Checkout failed. Please try again.'];
        }
    }
}

<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\Order;
use common\services\CartService;
use frontend\models\CheckoutForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class OrderController extends Controller
{
    use CustomerAppLayoutTrait;

    protected function getCustomerNavKey(): ?string
    {
        return 'orders';
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [['allow' => true, 'roles' => ['@']]],
            ],
        ];
    }

    public function actionCheckout(): string|Response
    {
        $cart = new CartService();
        $userId = Yii::$app->user->id;
        $items = $cart->getItems($userId);

        if ($items === []) {
            Yii::$app->session->setFlash('error', 'Your cart is empty.');

            return $this->redirect(['menu/index']);
        }

        $model = new CheckoutForm();
        $subtotal = $cart->getSubtotal($userId);
        $taxRate = (float) Yii::$app->params['restaurant.taxRate'];

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $order = $cart->checkout(
                $userId,
                $model->payment_method,
                $model->delivery_type,
                $model->notes,
            );

            if ($order !== null) {
                Yii::$app->session->setFlash('success', 'Order placed successfully! Order #' . $order->order_number);

                return $this->redirect(['track', 'id' => $order->id]);
            }

            Yii::$app->session->setFlash('error', 'Checkout failed. Please try again.');
        }

        return $this->render('checkout', [
            'model' => $model,
            'items' => $items,
            'subtotal' => $subtotal,
            'tax' => round($subtotal * $taxRate, 2),
            'total' => round($subtotal * (1 + $taxRate), 2),
        ]);
    }

    public function actionMyOrders(): string
    {
        $orders = Order::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->render('my-orders', ['orders' => $orders]);
    }

    public function actionTrack(int $id): string
    {
        $order = Order::find()
            ->where(['id' => $id, 'user_id' => Yii::$app->user->id])
            ->with('items')
            ->one();

        if ($order === null) {
            throw new NotFoundHttpException('Order not found.');
        }

        return $this->render('track', ['order' => $order]);
    }
}

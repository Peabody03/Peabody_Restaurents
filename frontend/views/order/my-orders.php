<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var \common\models\Order[] $orders */

use common\models\Order;
use yii\helpers\Html;

$this->title = 'My Orders';
?>
<div class="orders-page py-4 animate-fade-in">
    <h1 class="h2 fw-bold mb-4"><?= Html::encode($this->title) ?></h1>
    <?php if ($orders === []): ?>
        <div class="alert alert-info">No orders yet. <?= Html::a('Order now', ['menu/index']) ?></div>
    <?php else: ?>
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Order</th><th>Status</th><th>Payment</th><th>Total</th><th>Date</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= Html::encode($order->order_number) ?></td>
                            <td><span class="badge bg-primary"><?= Order::statuses()[$order->status] ?? $order->status ?></span></td>
                            <td><?= Html::encode($order->getPaymentMethodLabel()) ?></td>
                            <td><?= $order->getFormattedTotal() ?></td>
                            <td><?= Yii::$app->formatter->asDatetime($order->created_at, 'short') ?></td>
                            <td><?= Html::a('Track', ['track', 'id' => $order->id], ['class' => 'btn btn-sm btn-outline-primary']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif ?>
</div>

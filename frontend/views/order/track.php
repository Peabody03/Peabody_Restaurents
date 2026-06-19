<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var \common\models\Order $order */

use common\models\Order;
use yii\helpers\Html;

$this->title = 'Track Order';
$steps = [Order::STATUS_PENDING, Order::STATUS_CONFIRMED, Order::STATUS_PREPARING, Order::STATUS_READY, Order::STATUS_DELIVERED];
$currentIdx = array_search($order->status, $steps, true);
if ($currentIdx === false) $currentIdx = 0;
?>
<div class="track-page py-4 animate-fade-in">
    <h1 class="h2 fw-bold mb-1">Order #<?= Html::encode($order->order_number) ?></h1>
    <p class="text-muted mb-4">Placed <?= Yii::$app->formatter->asDatetime($order->created_at) ?></p>

    <div class="card border-0 shadow-sm p-4 mb-4">
        <div class="order-tracker d-flex justify-content-between flex-wrap gap-2 mb-4">
            <?php foreach ($steps as $i => $step): ?>
                <div class="tracker-step <?= $i <= $currentIdx ? 'active' : '' ?>">
                    <div class="tracker-dot"></div>
                    <small><?= Order::statuses()[$step] ?></small>
                </div>
            <?php endforeach; ?>
        </div>
        <p class="mb-0">Current status: <strong class="text-primary"><?= Order::statuses()[$order->status] ?? $order->status ?></strong></p>
        <p class="mb-0 mt-2">Payment:
            <span class="payment-badge">
                <span class="payment-brand payment-brand--<?= Html::encode($order->getPaymentMethodBrand()) ?>">
                    <?= $this->render('_payment-brand', ['brand' => $order->getPaymentMethodBrand()]) ?>
                </span>
                <?= Html::encode($order->getPaymentMethodLabel()) ?>
            </span>
        </p>
    </div>

    <div class="card border-0 shadow-sm p-4">
        <h2 class="h5 fw-bold mb-3">Order Items</h2>
        <?php foreach ($order->items as $item): ?>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span><?= Html::encode($item->food_name) ?> &times; <?= $item->quantity ?></span>
                <span>TZS <?= number_format((float) $item->total_price, 0) ?></span>
            </div>
        <?php endforeach; ?>
        <div class="d-flex justify-content-between fw-bold fs-5 mt-3">
            <span>Total</span>
            <span><?= $order->getFormattedTotal() ?></span>
        </div>
    </div>

    <?= Html::a('View All Orders', ['my-orders'], ['class' => 'btn btn-outline-secondary mt-3']) ?>
</div>

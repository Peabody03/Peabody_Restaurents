<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var \common\models\Order $model */

use common\models\Order;
use yii\helpers\Html;

$this->title = 'Order ' . $model->order_number;
?>
<div class="admin-panel p-4">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h4 fw-bold"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">Customer: <?= Html::encode($model->user->username ?? '—') ?></p>
        </div>
        <?= Html::a('Back', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?= Html::beginForm(['update-status', 'id' => $model->id], 'post', ['class' => 'row g-2 align-items-end mb-4']) ?>
    <div class="col-auto">
        <label class="form-label">Update Status</label>
        <?= Html::dropDownList('status', $model->status, Order::statuses(), ['class' => 'form-select']) ?>
    </div>
    <div class="col-auto">
        <?= Html::submitButton('Update', ['class' => 'btn btn-admin']) ?>
    </div>
    <?= Html::endForm() ?>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="small text-muted">Payment Method</div>
            <div class="fw-semibold"><?= Html::encode($model->getPaymentMethodLabel()) ?></div>
        </div>
        <div class="col-md-4">
            <div class="small text-muted">Payment Status</div>
            <div class="fw-semibold text-capitalize"><?= Html::encode($model->payment_status) ?></div>
        </div>
        <div class="col-md-4">
            <div class="small text-muted">Delivery</div>
            <div class="fw-semibold text-capitalize"><?= Html::encode($model->delivery_type) ?></div>
        </div>
    </div>

    <table class="table">
        <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
        <tbody>
        <?php foreach ($model->items as $item): ?>
            <tr>
                <td><?= Html::encode($item->food_name) ?></td>
                <td><?= $item->quantity ?></td>
                <td>TZS <?= number_format((float) $item->unit_price, 0) ?></td>
                <td>TZS <?= number_format((float) $item->total_price, 0) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr><td colspan="3" class="text-end">Subtotal</td><td>TZS <?= number_format((float) $model->subtotal, 0) ?></td></tr>
        <tr><td colspan="3" class="text-end">Tax</td><td>TZS <?= number_format((float) $model->tax, 0) ?></td></tr>
        <tr><td colspan="3" class="text-end fw-bold">Total</td><td class="fw-bold"><?= $model->getFormattedTotal() ?></td></tr>
        </tfoot>
    </table>
</div>

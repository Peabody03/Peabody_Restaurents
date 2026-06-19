<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var array $stats */
/** @var array $topItems */
/** @var \common\models\Order[] $orders */
/** @var string $from */
/** @var string $to */

use yii\helpers\Html;

$this->title = 'Sales Reports';
$fmt = static fn (float $n): string => 'TZS ' . number_format($n, 0, '.', ',');
?>
<h1 class="page-title mb-4"><?= Html::encode($this->title) ?></h1>

<form class="row g-2 mb-4" method="get">
    <div class="col-auto"><input type="date" name="from" class="form-control" value="<?= Html::encode($from) ?>"></div>
    <div class="col-auto"><input type="date" name="to" class="form-control" value="<?= Html::encode($to) ?>"></div>
    <div class="col-auto"><button type="submit" class="btn btn-admin">Filter</button></div>
</form>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="kpi-card"><div><p class="kpi-label">Revenue</p><p class="kpi-value"><?= $fmt($stats['totalRevenue']) ?></p></div></div></div>
    <div class="col-md-3"><div class="kpi-card"><div><p class="kpi-label">Orders</p><p class="kpi-value"><?= (int) $stats['totalOrders'] ?></p></div></div></div>
    <div class="col-md-3"><div class="kpi-card"><div><p class="kpi-label">Items Sold</p><p class="kpi-value"><?= (int) $stats['itemsSold'] ?></p></div></div></div>
    <div class="col-md-3"><div class="kpi-card"><div><p class="kpi-label">Avg Order</p><p class="kpi-value"><?= $fmt($stats['avgOrder']) ?></p></div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="admin-panel p-3">
            <h2 class="panel-title mb-3">Top Items</h2>
            <ul class="list-group list-group-flush">
                <?php foreach ($topItems as $item): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><?= Html::encode($item['food_name']) ?></span>
                        <span><?= (int) $item['total_qty'] ?> sold</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="admin-panel p-3">
            <h2 class="panel-title mb-3">Orders in Period</h2>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>Order</th><th>Total</th><th>Date</th></tr></thead>
                    <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= Html::encode($order->order_number) ?></td>
                            <td><?= $order->getFormattedTotal() ?></td>
                            <td><?= Yii::$app->formatter->asDate($order->created_at) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

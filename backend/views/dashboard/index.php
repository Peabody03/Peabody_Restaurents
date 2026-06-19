<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var array $stats */
/** @var \common\models\Order[] $recentOrders */
/** @var array $topItems */
/** @var string $selectedDate */

use common\models\Order;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Live Insights';
$displayName = Yii::$app->params['restaurant.displayName'] ?? 'Peabody_Restaurent';
$isToday = $selectedDate === date('Y-m-d');
$dateLabel = $isToday ? 'Today' : Yii::$app->formatter->asDate($selectedDate, 'long');
$fmt = static fn (float $n): string => 'TZS ' . number_format($n, 0, '.', ',');
$trend = static fn (float $v): string => ($v >= 0 ? '+' : '') . $v . '%';
?>
<div class="dashboard-live" id="dashboard-live" data-date="<?= Html::encode($selectedDate) ?>" data-api="<?= Url::to(['dashboard/live-api']) ?>">

    <!-- Header Row -->
    <div class="dashboard-header-row mb-4">
        <div class="dashboard-header-text">
            <p class="dashboard-eyebrow mb-1">Live Insights</p>
            <h1 class="dashboard-restaurant-name"><?= Html::encode($displayName) ?></h1>
            <p class="dashboard-subtitle">Welcome back — here's what's happening at your restaurant right now.</p>
        </div>
        <div class="dashboard-header-controls">
            <select class="form-select dashboard-filter" id="branch-select" aria-label="Branch selector">
                <option selected>All Branches</option>
                <option disabled>Kilimanjaro Bistro</option>
                <option disabled>Dar es Salaam Main</option>
            </select>
            <form method="get" class="dashboard-date-form" id="date-filter-form">
                <div class="dashboard-date-chip">
                    <span class="date-chip-label"><?= Html::encode($dateLabel) ?></span>
                    <span class="date-chip-sep">/</span>
                    <input type="date" name="date" id="dashboard-date" class="form-control form-control-sm border-0 bg-transparent p-0" value="<?= Html::encode($selectedDate) ?>" aria-label="Select date">
                </div>
            </form>
            <?= Html::a('<i class="bi bi-file-earmark-bar-graph me-1"></i> Generate Report', ['report/index', 'from' => $selectedDate, 'to' => $selectedDate], ['class' => 'btn btn-admin btn-generate-report']) ?>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="row g-3 mb-4 kpi-grid" id="kpi-cards">
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-card kpi-card-revenue">
                <div class="kpi-card-top">
                    <div class="kpi-icon-wrap kpi-icon-orange"><i class="bi bi-currency-exchange"></i></div>
                    <span class="kpi-trend text-success" data-trend="revenueGrowth"><?= $trend($stats['revenueGrowth']) ?></span>
                </div>
                <p class="kpi-label">Total Revenue</p>
                <p class="kpi-value" data-stat="totalRevenue"><?= $fmt($stats['totalRevenue']) ?></p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-card kpi-card-avg">
                <div class="kpi-card-top">
                    <div class="kpi-icon-wrap kpi-icon-blue"><i class="bi bi-basket2"></i></div>
                    <span class="kpi-trend text-success" data-trend="avgGrowth"><?= $trend($stats['avgGrowth']) ?></span>
                </div>
                <p class="kpi-label">Average Order</p>
                <p class="kpi-value" data-stat="avgOrder"><?= $fmt($stats['avgOrder']) ?></p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-card kpi-card-orders">
                <div class="kpi-card-top">
                    <div class="kpi-icon-wrap kpi-icon-green"><i class="bi bi-receipt"></i></div>
                    <span class="kpi-trend text-success" data-trend="ordersGrowth"><?= $trend($stats['ordersGrowth']) ?></span>
                </div>
                <p class="kpi-label">Total Orders</p>
                <p class="kpi-value" data-stat="totalOrders"><?= (int) $stats['totalOrders'] ?></p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-card kpi-card-items">
                <div class="kpi-card-top">
                    <div class="kpi-icon-wrap kpi-icon-purple"><i class="bi bi-box-seam"></i></div>
                    <span class="kpi-trend text-success" data-trend="itemsGrowth"><?= $trend($stats['itemsGrowth']) ?></span>
                </div>
                <p class="kpi-label">Items Sold</p>
                <p class="kpi-value" data-stat="itemsSold"><?= (int) $stats['itemsSold'] ?></p>
            </div>
        </div>
    </div>

    <!-- Data Summary Row -->
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="admin-panel admin-panel-elevated">
                <div class="panel-header">
                    <div>
                        <h2 class="panel-title">Recent Orders</h2>
                        <p class="panel-subtitle mb-0">Real-time order feed</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <small class="text-muted" id="last-updated">Updated just now</small>
                        <?= Html::a('View all', ['order/index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 dashboard-table">
                        <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Time</th>
                        </tr>
                        </thead>
                        <tbody id="recent-orders-body">
                        <?php if ($recentOrders === []): ?>
                            <tr class="empty-row"><td colspan="5" class="text-center text-muted py-5">No orders for this date yet.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td><?= Html::a(Html::encode($order->order_number), ['order/view', 'id' => $order->id], ['class' => 'order-link']) ?></td>
                                <td><?= Html::encode($order->user->username ?? '—') ?></td>
                                <td><span class="status-badge status-<?= $order->status ?>"><?= Order::statuses()[$order->status] ?? $order->status ?></span></td>
                                <td class="fw-semibold"><?= $order->getFormattedTotal() ?></td>
                                <td class="text-muted small"><?= Yii::$app->formatter->asDatetime($order->created_at, 'short') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="admin-panel admin-panel-elevated h-100">
                <div class="panel-header">
                    <div>
                        <h2 class="panel-title">Top Selling Items</h2>
                        <p class="panel-subtitle mb-0">Best performers today</p>
                    </div>
                </div>
                <ul class="top-items-list list-unstyled mb-0" id="top-items-list">
                    <?php if ($topItems === []): ?>
                        <li class="top-item-empty text-muted text-center py-5">No sales data yet.</li>
                    <?php endif; ?>
                    <?php foreach ($topItems as $i => $item): ?>
                        <li class="top-item-row">
                            <div class="top-item-rank"><?= $i + 1 ?></div>
                            <div class="top-item-info">
                                <span class="top-item-name"><?= Html::encode($item['food_name']) ?></span>
                                <span class="top-item-sales">TZS <?= number_format((float) $item['total_sales'], 0) ?></span>
                            </div>
                            <span class="top-item-badge"><?= (int) $item['total_qty'] ?> sold</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJsFile('@web/js/dashboard-live.js', ['depends' => [\backend\assets\AdminAsset::class]]);
$this->registerJs(<<<JS
document.getElementById('dashboard-date')?.addEventListener('change', function() {
    document.getElementById('date-filter-form').submit();
});
JS);

<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var array $snapshot */
/** @var string $displayName */
/** @var string $username */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Live Insights';

$liveDataUrl = Url::to(['/dashboard/live-data']);
$menuUrl = Url::to(['/menu/index']);
$accountUrl = Url::to(['/account/settings']);

$this->registerJs(
    "document.body.dataset.liveUrl = " . json_encode($liveDataUrl) . ";\n" .
    "document.body.dataset.menuUrl = " . json_encode($menuUrl) . ";",
    \yii\web\View::POS_READY,
);

$todayLabel = Yii::$app->formatter->asDate(time(), 'php:j F Y');
?>
<div class="cd-dash-header">
    <div>
        <div class="cd-live-badge">
            <span class="cd-live-dot"></span>
            Live Insights
            <span class="text-muted" id="lastUpdated" style="font-weight:500;margin-left:0.25rem;">· Auto-refresh every 10s</span>
        </div>
        <h1><?= Html::encode($displayName) ?></h1>
        <p class="cd-subtitle">Welcome back, <?= Html::encode($username) ?> — here's your activity overview.</p>
    </div>

    <div class="cd-dash-filters">
        <select class="cd-filter-select" id="branchFilter" aria-label="Branch">
            <option value="all">All Branches</option>
            <option value="main">Main Branch</option>
            <option value="downtown">Downtown</option>
        </select>
        <select class="cd-filter-date" id="dateFilter" aria-label="Date range">
            <option value="today">Today / <?= Html::encode($todayLabel) ?></option>
            <option value="week">This Week</option>
            <option value="month">This Month</option>
        </select>
        <?= Html::a('👤 Account', $accountUrl, ['class' => 'cd-btn-account']) ?>
    </div>
</div>

<!-- KPI Row -->
<div class="cd-kpi-grid">
    <div class="cd-kpi-card">
        <p class="cd-kpi-label">Average Order (TZS)</p>
        <p class="cd-kpi-value" id="kpiAvgOrder" data-current="<?= (int) $snapshot['avg_order_value'] ?>">
            TZS <?= number_format((int) $snapshot['avg_order_value'], 0, '.', ',') ?>
        </p>
        <span class="cd-kpi-trend up">Your spending average</span>
    </div>
    <div class="cd-kpi-card">
        <p class="cd-kpi-label">Total Orders (Count)</p>
        <p class="cd-kpi-value" id="kpiTotalOrders" data-current="<?= (int) $snapshot['total_orders'] ?>">
            <?= number_format((int) $snapshot['total_orders']) ?>
        </p>
        <p class="cd-kpi-meta">Cart items: <?= (int) $snapshot['cart_count'] ?></p>
        <span class="cd-kpi-trend neutral">Today filter applied</span>
    </div>
</div>

<!-- Data panels -->
<div class="cd-panels-grid">
    <section class="cd-panel">
        <div class="cd-panel-header">
            <h2>Recent Orders</h2>
            <?= Html::a('View all →', ['/order/my-orders'], ['class' => 'text-sm', 'style' => 'color:var(--cd-primary);text-decoration:none;font-size:0.8125rem;font-weight:600;']) ?>
        </div>
        <div class="cd-panel-body">
            <div style="overflow-x:auto;">
                <table class="cd-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody id="recentOrdersBody" data-order-ids="<?= Html::encode(json_encode(array_column($snapshot['recent_orders'], 'id'))) ?>">
                        <?php if ($snapshot['recent_orders'] === []): ?>
                            <tr>
                                <td colspan="5" class="cd-empty-state">
                                    No orders yet.
                                    <?= Html::a('Browse the menu', $menuUrl) ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($snapshot['recent_orders'] as $order): ?>
                                <tr>
                                    <td><strong><?= Html::encode($order['order_number']) ?></strong></td>
                                    <td><?= Html::encode($order['items_summary']) ?></td>
                                    <td><?= Html::encode($order['total']) ?></td>
                                    <td>
                                        <span class="cd-status-badge <?= Html::encode($order['status']) ?>">
                                            <?= Html::encode($order['status_label']) ?>
                                        </span>
                                    </td>
                                    <td style="color:var(--cd-text-muted);"><?= Html::encode($order['date']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="cd-panel">
        <div class="cd-panel-header">
            <h2>Top Buying Items</h2>
        </div>
        <div class="cd-panel-body" id="topItemsList">
            <?php if ($snapshot['top_items'] === []): ?>
                <div class="cd-empty-state">No purchase history yet.</div>
            <?php else: ?>
                <?php
                $maxQty = max(array_column($snapshot['top_items'], 'qty')) ?: 1;
                foreach ($snapshot['top_items'] as $idx => $item):
                    $pct = round(($item['qty'] / $maxQty) * 100);
                ?>
                    <div class="cd-top-item">
                        <span class="cd-top-rank"><?= $idx + 1 ?></span>
                        <div class="cd-top-info">
                            <span class="cd-top-name"><?= Html::encode($item['name']) ?></span>
                            <span class="cd-top-meta"><?= (int) $item['qty'] ?> purchased · <?= Html::encode($item['revenue']) ?></span>
                        </div>
                        <div class="cd-top-bar-wrap">
                            <div class="cd-top-bar" style="width:<?= $pct ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>

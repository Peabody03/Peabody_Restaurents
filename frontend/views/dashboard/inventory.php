<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Inventory';
?>
<div class="cd-dash-header">
    <div>
        <h1>My Inventory</h1>
        <p class="cd-subtitle">Quick view of items you've ordered most — your personal food pantry.</p>
    </div>
    <?= Html::a('Order More →', ['/menu/index'], ['class' => 'cd-btn-account']) ?>
</div>

<div class="cd-inventory-grid">
    <div class="cd-inventory-card">
        <div style="font-size:2rem;margin-bottom:0.5rem;">🥞</div>
        <strong>Breakfast Staples</strong>
        <p class="text-muted text-sm mt-2">Pancakes, eggs, pastries</p>
        <p class="text-sm" style="color:var(--cd-primary);font-weight:600;margin-top:0.75rem;">12 items tracked</p>
    </div>
    <div class="cd-inventory-card">
        <div style="font-size:2rem;margin-bottom:0.5rem;">🍕</div>
        <strong>Lunch Favorites</strong>
        <p class="text-muted text-sm mt-2">Pizza, salads, sandwiches</p>
        <p class="text-sm" style="color:var(--cd-primary);font-weight:600;margin-top:0.75rem;">8 items tracked</p>
    </div>
    <div class="cd-inventory-card">
        <div style="font-size:2rem;margin-bottom:0.5rem;">🐟</div>
        <strong>Dinner Picks</strong>
        <p class="text-muted text-sm mt-2">Salmon, steaks, pasta</p>
        <p class="text-sm" style="color:var(--cd-primary);font-weight:600;margin-top:0.75rem;">6 items tracked</p>
    </div>
    <div class="cd-inventory-card">
        <div style="font-size:2rem;margin-bottom:0.5rem;">🍗</div>
        <strong>Bits & Snacks</strong>
        <p class="text-muted text-sm mt-2">Wings, sliders, appetizers</p>
        <p class="text-sm" style="color:var(--cd-primary);font-weight:600;margin-top:0.75rem;">5 items tracked</p>
    </div>
</div>

<div class="cd-panel mt-4" style="margin-top:1.75rem;">
    <div class="cd-panel-header">
        <h2>Reorder Suggestions</h2>
    </div>
    <div class="cd-panel-body">
        <div class="cd-empty-state">
            Your top items from the dashboard will appear here for quick reorder.
            <?= Html::a('Go to Dashboard', Url::to(['/dashboard/index']), ['style' => 'display:block;margin-top:0.5rem;color:var(--cd-primary);']) ?>
        </div>
    </div>
</div>

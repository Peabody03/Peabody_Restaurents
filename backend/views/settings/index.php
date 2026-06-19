<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Settings';
$links = [
    ['Customers', 'customer/index', 'bi-people', 'Manage registered customers'],
    ['Reports', 'report/index', 'bi-bar-chart-line', 'Sales and performance reports'],
    ['Reservations', 'reservation/index', 'bi-calendar-check', 'Table booking management'],
    ['Image Gallery', 'image/index', 'bi-images', 'Upload and manage food images'],
    ['Menu Management', 'food/index', 'bi-cup-hot', 'Add, edit, and remove menu items'],
];
?>
<h1 class="page-title mb-1"><?= Html::encode($this->title) ?></h1>
<p class="text-muted mb-4">Restaurant configuration and management tools.</p>

<div class="row g-3 mb-4">
    <?php foreach ($links as [$title, $route, $icon, $desc]): ?>
        <div class="col-md-6 col-lg-4">
            <a href="<?= \yii\helpers\Url::to([$route]) ?>" class="settings-card">
                <i class="bi <?= $icon ?> settings-card-icon"></i>
                <div>
                    <strong><?= Html::encode($title) ?></strong>
                    <p class="text-muted small mb-0"><?= Html::encode($desc) ?></p>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<div class="admin-panel p-4" style="max-width:640px">
    <h2 class="h5 fw-bold mb-3">Restaurant Profile</h2>
    <?= Html::beginForm(['save'], 'post') ?>
    <div class="mb-3">
        <label class="form-label">Restaurant Name</label>
        <input type="text" class="form-control" value="<?= Html::encode(Yii::$app->params['restaurant.displayName']) ?>" readonly>
    </div>
    <div class="mb-3">
        <label class="form-label">Tax Rate</label>
        <input type="text" class="form-control" value="<?= (float) Yii::$app->params['restaurant.taxRate'] * 100 ?>%" readonly>
    </div>
    <div class="mb-3">
        <label class="form-label">Currency</label>
        <input type="text" class="form-control" value="TZS (Tanzanian Shilling)" readonly>
    </div>
    <button type="submit" class="btn btn-admin">Save Settings</button>
    <?= Html::endForm() ?>
</div>

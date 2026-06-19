<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;

$controller = Yii::$app->controller->id;

$nav = [
    ['dashboard', 'bi-speedometer2', 'Dashboard'],
    ['pos', 'bi-cash-stack', 'POS'],
    ['order', 'bi-receipt-cutoff', 'Orders & Kitchen'],
    ['food', 'bi-cup-hot', 'Bar & Menu Management'],
    ['inventory', 'bi-boxes', 'Inventory'],
    ['settings', 'bi-gear', 'Settings'],
];
?>
<aside class="admin-sidebar">
    <div class="sidebar-brand">
        <span class="brand-icon" aria-hidden="true">P</span>
        <span class="brand-text">PEABODY</span>
    </div>
    <nav class="sidebar-nav" aria-label="Admin navigation">
        <?php foreach ($nav as [$ctrl, $icon, $label]): ?>
            <?php $active = $controller === $ctrl ? 'active' : ''; ?>
            <a href="<?= Url::to([$ctrl . '/index']) ?>" class="sidebar-link <?= $active ?>">
                <i class="bi <?= $icon ?>"></i>
                <span><?= Html::encode($label) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-footer">
        <?= Html::beginForm(['/site/logout'], 'post') ?>
        <button type="submit" class="sidebar-link sidebar-logout w-100 border-0 bg-transparent text-start">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </button>
        <?= Html::endForm() ?>
    </div>
</aside>

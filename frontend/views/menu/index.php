<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var array $menuTypes */
/** @var array $counts */

use common\models\Food;
use common\services\ImageUploadService;
use yii\helpers\Html;

$this->title = 'Our Menu';
$uploads = new ImageUploadService();
$baseUrl = $uploads->getPublicUrl('categories');
?>
<div class="menu-home animate-fade-in">
    <section class="menu-hero mb-4 text-center">
        <p class="menu-hero-eyebrow">Premium Dining Experience</p>
        <h1 class="menu-hero-title"><?= Html::encode(Yii::$app->params['restaurant.name']) ?></h1>
        <p class="menu-hero-subtitle">Discover chef-crafted meals and order your favorites in seconds.</p>
        <div class="menu-hero-meta">
            <span>Fresh Daily</span>
            <span>Fast Delivery</span>
            <span>Top Rated</span>
        </div>
        <?php if (Yii::$app->user->isGuest): ?>
            <div class="menu-hero-actions">
                <?= Html::a('Sign In', ['site/login'], ['class' => 'btn btn-light']) ?>
                <?= Html::a('Create Account', ['site/signup'], ['class' => 'btn btn-outline-light']) ?>
            </div>
        <?php endif ?>
    </section>

    <div class="menu-category-grid">
        <?php foreach ($menuTypes as $key => $label): ?>
            <article class="menu-category-card">
                <div class="menu-category-image">
                    <img src="<?= Html::encode($baseUrl . '/' . $key . '.svg') ?>" alt="<?= Html::encode($label) ?>">
                </div>
                <div class="menu-category-body">
                    <h2 class="menu-category-title"><?= Html::encode($label) ?></h2>
                    <p class="menu-category-count"><?= (int) ($counts[$key] ?? 0) ?> items available</p>
                    <?= Html::a('View', ['category', 'type' => $key], ['class' => 'btn btn-menu-view w-100']) ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</div>

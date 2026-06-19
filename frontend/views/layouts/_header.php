<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use common\services\CartService;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Html;

$cartCount = 0;
if (!Yii::$app->user->isGuest) {
    $cartCount = (new CartService())->getCount((int) Yii::$app->user->id);
}

$items = [
    [
        'label' => 'Dashboard',
        'url' => ['/dashboard/index'],
        'visible' => !Yii::$app->user->isGuest,
    ],
    ['label' => 'Menu', 'url' => ['/menu/index']],
    [
        'label' => 'Cart' . ($cartCount > 0 ? ' <span class="badge bg-warning text-dark">' . $cartCount . '</span>' : ''),
        'url' => ['/cart/index'],
        'visible' => !Yii::$app->user->isGuest,
    ],
    [
        'label' => 'My Orders',
        'url' => ['/order/my-orders'],
        'visible' => !Yii::$app->user->isGuest,
    ],
    [
        'label' => 'Reservations',
        'url' => ['/reservation/my-reservations'],
        'visible' => !Yii::$app->user->isGuest,
    ],
    [
        'label' => 'Account',
        'url' => ['/account/settings'],
        'visible' => !Yii::$app->user->isGuest,
    ],
    ['label' => 'Sign Up', 'url' => ['/site/signup'], 'visible' => Yii::$app->user->isGuest],
    ['label' => 'Sign In', 'url' => ['/site/login'], 'visible' => Yii::$app->user->isGuest],
    [
        'label' => 'Sign Out (' . Html::encode(Yii::$app->user->identity?->username) . ')',
        'url' => ['/site/logout'],
        'linkOptions' => ['data-method' => 'post', 'class' => 'logout'],
        'visible' => !Yii::$app->user->isGuest,
    ],
];
?>
<header id="header">
    <?php NavBar::begin([
        'brandLabel' => Yii::$app->params['restaurant.name'],
        'brandUrl' => Yii::$app->user->isGuest ? ['/menu/index'] : ['/dashboard/index'],
        'options' => ['class' => 'navbar-expand-md navbar-dark auth-navbar fixed-top'],
    ]) ?>
    <?= Nav::widget([
        'options' => ['class' => 'navbar-nav me-auto'],
        'encodeLabels' => false,
        'items' => $items,
    ]) ?>
    <?= Html::button('&#127769;', [
        'id' => 'theme-toggle',
        'class' => 'btn btn-link nav-link fs-5',
        'aria-label' => 'Switch to dark mode',
    ]) ?>
    <?php NavBar::end() ?>
</header>

<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use common\models\User;
use yii\helpers\Html;

/** @var User|null $user */
$user = Yii::$app->user->identity;
$initials = strtoupper(substr((string) ($user?->username ?? 'C'), 0, 2));
?>
<header class="customer-topbar">
    <button type="button" class="customer-sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    <form class="customer-search" action="<?= Html::encode(\yii\helpers\Url::to(['/menu/index'])) ?>" method="get" role="search">
        <span class="customer-search-icon" aria-hidden="true">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="7"/><path d="M20 20l-3-3"/>
            </svg>
        </span>
        <input type="search" name="q" placeholder="Search menu food..." aria-label="Search menu food">
    </form>

    <div class="customer-topbar-actions">
        <div class="customer-profile" tabindex="0">
            <div class="customer-profile-avatar"><?= Html::encode($initials) ?></div>
            <div class="customer-profile-info">
                <span class="customer-profile-name"><?= Html::encode($user?->username ?? 'Guest') ?></span>
                <span class="customer-profile-role">Customer</span>
            </div>
        </div>
    </div>
</header>

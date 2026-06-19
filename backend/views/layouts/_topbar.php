<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;

/** @var \common\models\User $user */
$user = Yii::$app->user->identity;
$searchQ = Html::encode(Yii::$app->request->get('q', ''));
?>
<header class="admin-topbar">
    <form class="topbar-search" action="<?= Url::to(['search/index']) ?>" method="get" role="search">
        <i class="bi bi-search" aria-hidden="true"></i>
        <input type="search" name="q" class="form-control" value="<?= $searchQ ?>" placeholder="Search orders, inventory, or customers..." aria-label="Global search">
    </form>
    <div class="topbar-actions">
        <span class="live-pulse d-none d-md-inline-flex" id="live-indicator" title="Live data active">
            <span class="live-dot"></span> Live
        </span>
        <div class="topbar-profile">
            <div class="topbar-avatar"><?= strtoupper(substr($user->username ?? 'A', 0, 1)) ?></div>
            <div class="topbar-profile-text">
                <span class="topbar-user"><?= Html::encode($user->username ?? 'Admin') ?></span>
                <span class="topbar-role">Restaurant Owner / Tenant Admin</span>
            </div>
        </div>
    </div>
</header>

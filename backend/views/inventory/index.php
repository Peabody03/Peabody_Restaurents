<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $menuTypes */
/** @var string $search */
/** @var int $available */
/** @var int $unavailable */

use common\models\Food;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Inventory';
?>
<div class="page-header-row mb-4">
    <div>
        <h1 class="page-title mb-1"><?= Html::encode($this->title) ?></h1>
        <p class="text-muted mb-0">Track menu item availability and stock status.</p>
    </div>
    <?= Html::a('<i class="bi bi-plus-lg"></i> Add Item', ['food/create'], ['class' => 'btn btn-admin']) ?>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="kpi-card"><div><p class="kpi-label">Available</p><p class="kpi-value text-success"><?= $available ?></p></div></div></div>
    <div class="col-md-4"><div class="kpi-card"><div><p class="kpi-label">Unavailable</p><p class="kpi-value text-danger"><?= $unavailable ?></p></div></div></div>
    <div class="col-md-4"><div class="kpi-card"><div><p class="kpi-label">Total Items</p><p class="kpi-value"><?= $available + $unavailable ?></p></div></div></div>
</div>

<form class="topbar-search mb-3 d-inline-flex" method="get" style="max-width:360px">
    <i class="bi bi-search"></i>
    <input type="search" name="q" value="<?= Html::encode($search) ?>" class="form-control" placeholder="Search inventory...">
</form>

<div class="admin-panel p-3">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-hover mb-0'],
        'columns' => [
            'food_name',
            ['attribute' => 'menu_type', 'value' => static fn (Food $m) => Food::menuTypes()[$m->menu_type] ?? $m->menu_type],
            ['attribute' => 'price', 'value' => static fn (Food $m) => $m->getFormattedPrice()],
            [
                'attribute' => 'is_available',
                'format' => 'raw',
                'value' => static fn (Food $m) => $m->is_available
                    ? '<span class="status-badge status-ready">In Stock</span>'
                    : '<span class="status-badge status-cancelled">Out of Stock</span>',
            ],
            [
                'class' => \yii\grid\ActionColumn::class,
                'template' => '{update}',
                'buttons' => ['update' => static fn ($url) => Html::a('Edit', $url, ['class' => 'btn btn-sm btn-outline-secondary'])],
            ],
        ],
    ]) ?>
</div>

<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $menuTypes */

use common\models\Food;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Menu Management';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title mb-1"><?= Html::encode($this->title) ?></h1>
        <p class="text-muted small mb-0">Click a food row to edit details and upload photos.</p>
    </div>
    <?= Html::a('<i class="bi bi-plus-lg"></i> Add Food', ['create'], ['class' => 'btn btn-admin']) ?>
</div>

<div class="mb-3 d-flex flex-wrap gap-2">
    <?= Html::a('All', ['index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
    <?php foreach ($menuTypes as $key => $label): ?>
        <?= Html::a($label, ['index', 'menu_type' => $key], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
    <?php endforeach; ?>
</div>

<div class="admin-panel p-3">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-hover mb-0'],
        'columns' => [
            [
                'attribute' => 'image',
                'format' => 'raw',
                'value' => static fn (Food $m) => Html::img($m->getImageUrl(), ['class' => 'food-thumb', 'alt' => '']),
            ],
            'food_name',
            [
                'attribute' => 'menu_type',
                'value' => static fn (Food $m) => Food::menuTypes()[$m->menu_type] ?? $m->menu_type,
            ],
            [
                'attribute' => 'price',
                'value' => static fn (Food $m) => $m->getFormattedPrice(),
            ],
            [
                'attribute' => 'is_available',
                'format' => 'raw',
                'value' => static fn (Food $m) => $m->is_available
                    ? '<span class="badge bg-success">Yes</span>'
                    : '<span class="badge bg-secondary">No</span>',
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{update} {delete}',
            ],
        ],
    ]) ?>
</div>

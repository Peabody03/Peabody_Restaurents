<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $statuses */

use common\models\Order;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Orders & Kitchen';
?>
<h1 class="page-title mb-4"><?= Html::encode($this->title) ?></h1>

<div class="mb-3 d-flex flex-wrap gap-2">
    <?= Html::a('All', ['index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
    <?php foreach ($statuses as $key => $label): ?>
        <?= Html::a($label, ['index', 'status' => $key], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
    <?php endforeach; ?>
</div>

<div class="admin-panel p-3">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-hover mb-0'],
        'columns' => [
            'order_number',
            ['attribute' => 'user.username', 'label' => 'Customer'],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => static fn (Order $m) => '<span class="badge status-' . $m->status . '">' . (Order::statuses()[$m->status] ?? $m->status) . '</span>',
            ],
            [
                'attribute' => 'payment_method',
                'label' => 'Payment',
                'value' => static fn (Order $m) => $m->getPaymentMethodLabel(),
            ],
            ['attribute' => 'total', 'value' => static fn (Order $m) => $m->getFormattedTotal()],
            ['attribute' => 'created_at', 'format' => ['datetime', 'short']],
            ['class' => ActionColumn::class, 'template' => '{view}'],
        ],
    ]) ?>
</div>

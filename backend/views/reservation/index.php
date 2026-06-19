<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use common\models\Reservation;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Reservations';
?>
<h1 class="page-title mb-4"><?= Html::encode($this->title) ?></h1>

<div class="admin-panel p-3">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-hover mb-0'],
        'columns' => [
            'guest_name',
            'guest_email',
            'guest_phone',
            'guests',
            ['attribute' => 'reservation_date', 'format' => ['date', 'medium']],
            [
                'attribute' => 'reservation_time',
                'value' => static fn (Reservation $m) => substr($m->reservation_time, 0, 5),
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => static function (Reservation $m) {
                    return Html::beginForm(['update-status', 'id' => $m->id], 'post', ['class' => 'd-flex gap-1'])
                        . Html::dropDownList('status', $m->status, [
                            'pending' => 'Pending',
                            'confirmed' => 'Confirmed',
                            'cancelled' => 'Cancelled',
                            'completed' => 'Completed',
                        ], ['class' => 'form-select form-select-sm'])
                        . Html::submitButton('Save', ['class' => 'btn btn-sm btn-admin'])
                        . Html::endForm();
                },
            ],
        ],
    ]) ?>
</div>

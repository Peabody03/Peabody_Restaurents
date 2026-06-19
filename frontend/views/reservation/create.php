<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var \frontend\models\ReservationForm $model */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Book a Table';
?>
<div class="py-4 animate-fade-in" style="max-width:560px;margin:0 auto">
    <h1 class="h2 fw-bold mb-1"><?= Html::encode($this->title) ?></h1>
    <p class="text-muted mb-4">Reserve your table at <?= Html::encode(Yii::$app->params['restaurant.name']) ?></p>

    <div class="card border-0 shadow-sm p-4">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'guest_name')->textInput() ?>
        <?= $form->field($model, 'guest_email')->textInput(['type' => 'email']) ?>
        <?= $form->field($model, 'guest_phone')->textInput() ?>
        <?= $form->field($model, 'guests')->input('number', ['min' => 1, 'max' => 50]) ?>
        <div class="row">
            <div class="col-md-6"><?= $form->field($model, 'reservation_date')->input('date', ['min' => date('Y-m-d')]) ?></div>
            <div class="col-md-6"><?= $form->field($model, 'reservation_time')->input('time') ?></div>
        </div>
        <?= $form->field($model, 'notes')->textarea(['rows' => 3]) ?>
        <?= Html::submitButton('Submit Reservation', ['class' => 'btn btn-menu w-100']) ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>

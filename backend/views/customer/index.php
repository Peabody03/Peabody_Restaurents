<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string $search */

use common\models\User;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Customers';
?>
<h1 class="page-title mb-4"><?= Html::encode($this->title) ?></h1>

<form class="mb-3 topbar-search d-inline-flex" method="get">
    <i class="bi bi-search"></i>
    <input type="search" name="q" value="<?= Html::encode($search) ?>" class="form-control" placeholder="Search customers...">
</form>

<div class="admin-panel p-3">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-hover mb-0'],
        'columns' => ['username', 'email', 'phone', ['attribute' => 'created_at', 'format' => ['date', 'medium']]],
    ]) ?>
</div>

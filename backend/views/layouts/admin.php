<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $content */

use backend\assets\AdminAsset;
use yii\helpers\Html;

AdminAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> — PEABODY Admin</title>
    <?php $this->head() ?>
</head>
<body class="admin-body">
<?php $this->beginBody() ?>
<div class="admin-wrapper">
    <?= $this->render('_sidebar') ?>
    <div class="admin-main">
        <?= $this->render('_topbar') ?>
        <main class="admin-content">
            <?= \common\widgets\Alert::widget() ?>
            <?= $content ?>
        </main>
    </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\CustomerDashboardAsset;
use yii\helpers\Html;

CustomerDashboardAsset::register($this);
$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> — Peabody</title>
    <?php $this->head() ?>
</head>
<body class="customer-app-body">
<?php $this->beginBody() ?>

<div class="customer-app">
    <?= $this->render('_customer-sidebar') ?>

    <div class="customer-main">
        <?= $this->render('_customer-topbar') ?>

        <main class="customer-content page-enter">
            <?= Alert::widget() ?>
            <?= $content ?>
        </main>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

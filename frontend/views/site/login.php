<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Login';
$htmlIcon = '{label}<div class="input-group"><span class="input-group-text" aria-hidden="true">%s</span>{input}</div>{error}{hint}';
$labelOptions = ['class' => 'form-label fw-semibold small'];

ob_start();
$form = ActiveForm::begin(['id' => 'login-form']);
?>
<div class="mb-3">
    <?= $form->field($model, 'username', [
        'options' => ['class' => 'mb-0'],
        'template' => sprintf($htmlIcon, '&#128100;'),
        'inputOptions' => ['class' => 'form-control', 'placeholder' => 'Username', 'autofocus' => true],
    ])->textInput()->label('Username', $labelOptions) ?>
</div>
<div class="mb-3">
    <?= $form->field($model, 'password', [
        'options' => ['class' => 'mb-0'],
        'template' => sprintf($htmlIcon, '&#128274;'),
        'inputOptions' => ['class' => 'form-control', 'placeholder' => 'Password'],
    ])->passwordInput()->label('Password', $labelOptions) ?>
</div>
<div class="mb-4">
    <?= $form->field($model, 'rememberMe')->checkbox() ?>
</div>
<div class="d-grid">
    <?= Html::submitButton('Sign In', ['class' => 'btn login-btn btn-lg rounded-3 text-white', 'name' => 'login-button']) ?>
</div>
<?php ActiveForm::end();
$content = ob_get_clean();

$footer = Html::a('Forgot your password?', ['site/request-password-reset'])
    . '<span class="mx-1">&middot;</span>'
    . Html::a('Create an account', ['site/signup']);

echo $this->render('//layouts/_authCard', [
    'title' => $this->title,
    'subtitle' => 'Sign in with your username and password',
    'brandTitle' => 'Welcome<br>Back',
    'brandText' => 'Sign in to manage your restaurant account securely.',
    'content' => $content,
    'footer' => $footer,
]);

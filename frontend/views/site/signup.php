<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\SignupForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Create Account';
$htmlIcon = '{label}<div class="input-group"><span class="input-group-text" aria-hidden="true">%s</span>{input}</div>{error}{hint}';
$labelOptions = ['class' => 'form-label fw-semibold small'];

ob_start();
$form = ActiveForm::begin(['id' => 'form-signup']);
?>
<div class="mb-3">
    <?= $form->field($model, 'username', [
        'options' => ['class' => 'mb-0'],
        'template' => sprintf($htmlIcon, '&#128100;'),
        'inputOptions' => ['class' => 'form-control', 'placeholder' => 'Username', 'autofocus' => true],
    ])->textInput()->label('Username', $labelOptions) ?>
</div>
<div class="mb-3">
    <?= $form->field($model, 'email', [
        'options' => ['class' => 'mb-0'],
        'template' => sprintf($htmlIcon, '&#9993;'),
        'inputOptions' => ['class' => 'form-control', 'placeholder' => 'email@example.com'],
    ])->textInput()->label('Email Address', $labelOptions) ?>
</div>
<div class="mb-3">
    <?= $form->field($model, 'phone', [
        'options' => ['class' => 'mb-0'],
        'template' => sprintf($htmlIcon, '&#128222;'),
        'inputOptions' => ['class' => 'form-control', 'placeholder' => '+1 (555) 000-0000'],
    ])->textInput()->label('Phone Number', $labelOptions) ?>
</div>
<div class="mb-3">
    <?= $form->field($model, 'password', [
        'options' => ['class' => 'mb-0'],
        'template' => sprintf($htmlIcon, '&#128274;'),
        'inputOptions' => ['class' => 'form-control', 'placeholder' => 'Password'],
    ])->passwordInput()->label('Password', $labelOptions) ?>
    <div class="form-text small">At least 8 characters with uppercase, lowercase, number, and special character.</div>
</div>
<div class="d-grid">
    <?= Html::submitButton('Create Account', ['class' => 'btn login-btn btn-lg rounded-3 text-white', 'name' => 'signup-button']) ?>
</div>
<?php ActiveForm::end();
$content = ob_get_clean();

echo $this->render('//layouts/_authCard', [
    'title' => $this->title,
    'subtitle' => 'Fill in your details to get started',
    'brandTitle' => 'Join<br>Us',
    'brandText' => 'Sign up in seconds — no verification required.',
    'content' => $content,
    'footer' => 'Already have an account? ' . Html::a('Sign in', ['site/login']),
]);

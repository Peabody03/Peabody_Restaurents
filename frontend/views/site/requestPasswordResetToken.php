<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var \frontend\models\PasswordResetRequestForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Forgot Password';
$htmlIcon = '{label}<div class="input-group"><span class="input-group-text" aria-hidden="true">%s</span>{input}</div>{error}{hint}';
$labelOptions = ['class' => 'form-label fw-semibold small'];

ob_start();
$form = ActiveForm::begin(['id' => 'request-password-reset-form']);
?>
<p class="text-muted small mb-3">Enter the email or phone number linked to your account. We'll send a reset link and code right away.</p>
<div class="mb-4">
    <?= $form->field($model, 'identifier', [
        'options' => ['class' => 'mb-0'],
        'template' => sprintf($htmlIcon, '&#128231;'),
        'inputOptions' => [
            'class' => 'form-control',
            'placeholder' => 'email@example.com or +255 700 000 000',
            'autofocus' => true,
        ],
    ])->textInput()->label('Email or Phone', $labelOptions) ?>
</div>
<div class="d-grid">
    <?= Html::submitButton('Send Reset Instructions', ['class' => 'btn login-btn btn-lg rounded-3 text-white']) ?>
</div>
<?php ActiveForm::end();
$content = ob_get_clean();

echo $this->render('//layouts/_authCard', [
    'title' => $this->title,
    'subtitle' => 'Quick and easy password recovery',
    'brandTitle' => 'Reset<br>Password',
    'brandText' => 'Get a reset link by email or a code by SMS in seconds.',
    'content' => $content,
    'footer' => 'Remember your password? ' . Html::a('Sign in', ['site/login']),
]);

<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\ResetPasswordForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'New Password';
$htmlIcon = '{label}<div class="input-group"><span class="input-group-text" aria-hidden="true">%s</span>{input}</div>{error}{hint}';
$labelOptions = ['class' => 'form-label fw-semibold small'];

ob_start();
$form = ActiveForm::begin(['id' => 'reset-password-form']);
?>
<div class="mb-3">
    <?= $form->field($model, 'password', [
        'options' => ['class' => 'mb-0'],
        'template' => sprintf($htmlIcon, '&#128274;'),
        'inputOptions' => ['class' => 'form-control', 'placeholder' => 'New password', 'autofocus' => true],
    ])->passwordInput()->label('New Password', $labelOptions) ?>
    <div class="form-text small">At least 8 characters with uppercase, lowercase, number, and special character.</div>
</div>
<div class="mb-4">
    <?= $form->field($model, 'passwordConfirm', [
        'options' => ['class' => 'mb-0'],
        'template' => sprintf($htmlIcon, '&#128274;'),
        'inputOptions' => ['class' => 'form-control', 'placeholder' => 'Confirm password'],
    ])->passwordInput()->label('Confirm Password', $labelOptions) ?>
</div>
<div class="d-grid">
    <?= Html::submitButton('Update Password', ['class' => 'btn login-btn btn-lg rounded-3 text-white']) ?>
</div>
<?php ActiveForm::end();
$content = ob_get_clean();

echo $this->render('//layouts/_authCard', [
    'title' => $this->title,
    'subtitle' => 'Choose a strong password for your account',
    'brandTitle' => 'Secure<br>Account',
    'brandText' => 'Set a new password to regain access to your account.',
    'content' => $content,
    'footer' => null,
]);

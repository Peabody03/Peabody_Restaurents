<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var \frontend\models\PasswordResetVerifyForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Enter Reset Code';
$expireMinutes = (int) (Yii::$app->params['user.otpExpire'] / 60);

ob_start();
$form = ActiveForm::begin(['id' => 'verify-password-reset-form']);
?>
<div class="alert alert-info small mb-3">
    <strong>Tip:</strong> You can also click the reset link in your email to skip this step.
    Codes expire after <?= $expireMinutes ?> minutes.
</div>
<div class="mb-3">
    <?= $form->field($model, 'identifier', [
        'inputOptions' => [
            'class' => 'form-control',
            'placeholder' => 'Email or phone number',
            'readonly' => !empty($model->identifier),
        ],
    ])->textInput()->label('Email or Phone') ?>
</div>
<div class="mb-4">
    <?= $form->field($model, 'otp', [
        'template' => '{label}<div class="otp-input-group">{input}</div>{error}{hint}',
        'inputOptions' => [
            'class' => 'form-control otp-input text-center',
            'placeholder' => '000000',
            'maxlength' => 6,
            'inputmode' => 'numeric',
            'autocomplete' => 'one-time-code',
            'autofocus' => true,
        ],
    ])->textInput()->label('6-Digit Reset Code') ?>
</div>
<div class="d-grid mb-3">
    <?= Html::submitButton('Verify & Continue', ['class' => 'btn login-btn btn-lg rounded-3 text-white']) ?>
</div>
<?php ActiveForm::end();

echo Html::beginForm(['site/resend-password-reset'], 'post', ['class' => 'text-center']);
echo Html::hiddenInput('identifier', $model->identifier);
echo Html::submitButton('Resend reset instructions', ['class' => 'btn btn-link btn-sm']);
echo Html::endForm();

$content = ob_get_clean();

echo $this->render('//layouts/_authCard', [
    'title' => $this->title,
    'subtitle' => 'Enter the code from your email or SMS',
    'brandTitle' => 'Almost<br>Done',
    'brandText' => 'Verify your code, then choose a new password.',
    'content' => $content,
    'footer' => Html::a('Back to sign in', ['site/login']),
]);

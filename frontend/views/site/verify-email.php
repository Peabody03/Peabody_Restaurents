<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\VerifyOtpForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Verify Email';
$otpExpireMinutes = (int) (Yii::$app->params['user.otpExpire'] / 60);
$htmlIcon = '{label}<div class="input-group"><span class="input-group-text" aria-hidden="true">%s</span>{input}</div>{error}{hint}';
$labelOptions = ['class' => 'form-label fw-semibold small'];

ob_start();
$form = ActiveForm::begin(['id' => 'verify-email-form']);
?>
<div class="alert alert-info small mb-4">
    Enter the 6-digit code sent to your email. Codes expire after <?= $otpExpireMinutes ?> minutes.
</div>
<div class="mb-3">
    <?= $form->field($model, 'email', [
        'options' => ['class' => 'mb-0'],
        'template' => sprintf($htmlIcon, '&#9993;'),
        'inputOptions' => ['class' => 'form-control', 'placeholder' => 'email@example.com', 'readonly' => !empty($model->email)],
    ])->textInput()->label('Email Address', $labelOptions) ?>
</div>
<div class="mb-4">
    <?= $form->field($model, 'otp', [
        'options' => ['class' => 'mb-0'],
        'template' => '{label}<div class="otp-input-group">{input}</div>{error}{hint}',
        'inputOptions' => [
            'class' => 'form-control otp-input text-center',
            'placeholder' => '000000',
            'maxlength' => 6,
            'inputmode' => 'numeric',
            'pattern' => '[0-9]*',
            'autocomplete' => 'one-time-code',
            'autofocus' => true,
        ],
    ])->textInput()->label('Verification Code', $labelOptions) ?>
</div>
<div class="d-grid mb-3">
    <?= Html::submitButton('Verify Account', ['class' => 'btn login-btn btn-lg rounded-3 text-white']) ?>
</div>
<?php ActiveForm::end();

echo Html::beginForm(['site/resend-verification-otp'], 'post', ['class' => 'text-center']);
echo Html::hiddenInput('email', $model->email);
echo Html::submitButton('Resend verification code', ['class' => 'btn btn-link btn-sm']);
echo Html::endForm();

$content = ob_get_clean();

echo $this->render('//layouts/_authCard', [
    'title' => $this->title,
    'subtitle' => 'Check your inbox for the verification code',
    'brandTitle' => 'Almost<br>There',
    'brandText' => 'Verify your email to activate your account.',
    'content' => $content,
    'footer' => Html::a('Back to login', ['site/login']),
]);

<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Admin Login';
?>
<div class="admin-login-page d-flex align-items-center justify-content-center min-vh-100" style="background:#f4f6f9">
    <div class="card border-0 shadow-lg p-4 p-md-5" style="max-width:420px;width:100%;border-radius:20px">
        <div class="text-center mb-4">
            <div class="brand-icon mx-auto mb-3" style="width:56px;height:56px;background:linear-gradient(135deg,#f97316,#ea580c);color:#fff;border-radius:14px;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:1.5rem">P</div>
            <h1 class="h4 fw-bold">PEABODY Admin</h1>
            <p class="text-muted small">Restaurant Owner / Tenant Admin</p>
        </div>
        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
        <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => 'admin']) ?>
        <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Password']) ?>
        <?= $form->field($model, 'rememberMe')->checkbox() ?>
        <div class="d-grid">
            <?= Html::submitButton('Sign In to Dashboard', ['class' => 'btn btn-warning text-white fw-semibold btn-lg', 'style' => 'background:linear-gradient(135deg,#f97316,#ea580c);border:none']) ?>
        </div>
        <?php ActiveForm::end(); ?>
        <p class="text-center text-muted small mt-3 mb-0">Default: admin / Admin@12345</p>
    </div>
</div>

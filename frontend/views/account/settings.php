<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\AccountSettingsForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Account Settings';
$this->params['breadcrumbs'][] = ['label' => 'Dashboard', 'url' => ['dashboard/index']];
$this->params['breadcrumbs'][] = $this->title;

$htmlIcon = '{label}<div class="input-group"><span class="input-group-text" aria-hidden="true">%s</span>{input}</div>{error}{hint}';
$labelOptions = ['class' => 'form-label fw-semibold small'];
?>
<div class="account-settings-page py-4 animate-fade-in">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm form-card">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h3 fw-bold mb-1"><?= Html::encode($this->title) ?></h1>
                    <p class="text-body-secondary mb-4">Update your profile information and security settings.</p>

                    <?php $form = ActiveForm::begin(['id' => 'account-settings-form']); ?>

                    <h2 class="h5 fw-semibold mb-3">Profile Information</h2>
                    <div class="mb-3">
                        <?= $form->field($model, 'username', [
                            'options' => ['class' => 'mb-0'],
                            'template' => sprintf($htmlIcon, '&#128100;'),
                            'inputOptions' => ['class' => 'form-control'],
                        ])->textInput()->label('Username', $labelOptions) ?>
                    </div>
                    <div class="mb-3">
                        <?= $form->field($model, 'email', [
                            'options' => ['class' => 'mb-0'],
                            'template' => sprintf($htmlIcon, '&#9993;'),
                            'inputOptions' => ['class' => 'form-control'],
                        ])->textInput()->label('Email Address', $labelOptions) ?>
                    </div>
                    <div class="mb-4">
                        <?= $form->field($model, 'phone', [
                            'options' => ['class' => 'mb-0'],
                            'template' => sprintf($htmlIcon, '&#128222;'),
                            'inputOptions' => ['class' => 'form-control'],
                        ])->textInput()->label('Phone Number', $labelOptions) ?>
                    </div>

                    <h2 class="h5 fw-semibold mb-3" id="password-section">Change Password</h2>
                    <p class="text-body-secondary small mb-3">Leave blank to keep your current password.</p>
                    <div class="mb-3">
                        <?= $form->field($model, 'currentPassword', [
                            'options' => ['class' => 'mb-0'],
                            'template' => sprintf($htmlIcon, '&#128274;'),
                            'inputOptions' => ['class' => 'form-control'],
                        ])->passwordInput()->label('Current Password', $labelOptions) ?>
                    </div>
                    <div class="mb-3">
                        <?= $form->field($model, 'newPassword', [
                            'options' => ['class' => 'mb-0'],
                            'template' => sprintf($htmlIcon, '&#128274;'),
                            'inputOptions' => ['class' => 'form-control'],
                        ])->passwordInput()->label('New Password', $labelOptions) ?>
                    </div>
                    <div class="mb-4">
                        <?= $form->field($model, 'newPasswordConfirm', [
                            'options' => ['class' => 'mb-0'],
                            'template' => sprintf($htmlIcon, '&#128274;'),
                            'inputOptions' => ['class' => 'form-control'],
                        ])->passwordInput()->label('Confirm New Password', $labelOptions) ?>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <?= Html::submitButton('Save Changes', ['class' => 'btn login-btn text-white']) ?>
                        <?= Html::a('Back to Dashboard', ['dashboard/index'], ['class' => 'btn btn-outline-secondary']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

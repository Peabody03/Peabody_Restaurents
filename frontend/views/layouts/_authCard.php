<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $title */
/** @var string $subtitle */
/** @var string $brandTitle */
/** @var string $brandText */
/** @var string $content */
/** @var string|null $footer */

use yii\helpers\Html;

$brandTitle = $brandTitle ?? $title;
$brandText = $brandText ?? $subtitle;
?>
<div class="auth-page d-flex align-items-center justify-content-center py-5">
    <div class="card border-0 overflow-hidden login-split-card auth-card animate-fade-in">
        <div class="row g-0">
            <div class="col-md-5 d-none d-md-flex login-brand-panel text-white">
                <div class="d-flex flex-column justify-content-between p-4 p-lg-5 w-100">
                    <div>
                        <div class="auth-brand-icon mb-4">&#127869;</div>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-3 login-brand-title"><?= $brandTitle ?></h2>
                        <p class="opacity-75 mb-0 login-brand-text"><?= Html::encode($brandText) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="p-4 p-lg-5">
                    <div class="text-center mb-4">
                        <div class="d-md-none mb-3">
                            <div class="auth-brand-icon auth-brand-icon-sm">&#127869;</div>
                        </div>
                        <h1 class="h3 fw-bold mb-1"><?= Html::encode($title) ?></h1>
                        <?php if (!empty($subtitle)): ?>
                            <p class="text-body-secondary small"><?= Html::encode($subtitle) ?></p>
                        <?php endif ?>
                    </div>
                    <?= $content ?>
                    <?php if (!empty($footer)): ?>
                        <div class="text-body-secondary text-center mt-3 small">
                            <?= $footer ?>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>

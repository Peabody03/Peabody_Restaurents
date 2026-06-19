<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Secure Restaurant Portal';
?>
<div class="site-index animate-fade-in">
    <div class="hero-banner text-white rounded-4 p-5 mb-4 position-relative overflow-hidden">
        <div class="position-relative">
            <p class="text-uppercase small opacity-75 mb-2">Modern authentication</p>
            <h1 class="display-5 fw-bold mb-3">Manage your restaurant account securely</h1>
            <p class="lead opacity-75 mb-4 hero-lead">
                Register with email verification, sign in safely, and reset your password with one-time codes.
            </p>
            <div class="d-flex gap-2 flex-wrap">
                <?= Html::a('Create Account', ['site/signup'], ['class' => 'btn btn-light btn-lg fw-semibold px-4']) ?>
                <?= Html::a('Sign In', ['site/login'], ['class' => 'btn btn-outline-light btn-lg px-4']) ?>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm rounded-3 extension-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <span class="extension-icon">&#128274;</span>
                        <h2 class="h6 fw-bold mb-0 ms-2">Secure by design</h2>
                    </div>
                    <p class="text-body-secondary small mb-0">
                        Passwords are encrypted, sessions are protected, and login attempts are rate-limited.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm rounded-3 extension-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <span class="extension-icon">&#9993;</span>
                        <h2 class="h6 fw-bold mb-0 ms-2">Email verification</h2>
                    </div>
                    <p class="text-body-secondary small mb-0">
                        Accounts stay inactive until you verify your email with a time-limited one-time code.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm rounded-3 extension-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <span class="extension-icon">&#128241;</span>
                        <h2 class="h6 fw-bold mb-0 ms-2">Works everywhere</h2>
                    </div>
                    <p class="text-body-secondary small mb-0">
                        Responsive layouts optimized for mobile, tablet, and desktop devices.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

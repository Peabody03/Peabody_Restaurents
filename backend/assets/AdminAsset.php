<?php

declare(strict_types=1);

namespace backend\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;

class AdminAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
        'css/admin.css',
    ];
    public $js = [
        'js/admin.js',
    ];
    public $depends = [
        YiiAsset::class,
        'yii\bootstrap5\BootstrapAsset',
    ];
}

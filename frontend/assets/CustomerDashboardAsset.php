<?php

declare(strict_types=1);

namespace frontend\assets;

use yii\web\AssetBundle;

class CustomerDashboardAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/customer-dashboard.css',
    ];
    public $js = [
        'js/customer-dashboard.js',
    ];
    public $depends = [
        AppAsset::class,
    ];
}

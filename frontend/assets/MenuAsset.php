<?php

declare(strict_types=1);

namespace frontend\assets;

use yii\web\AssetBundle;

class MenuAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/menu-food-view.js',
    ];
    public $depends = [
        AppAsset::class,
    ];
}

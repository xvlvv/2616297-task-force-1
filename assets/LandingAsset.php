<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle для главной (лендинг) страницы.
 * Регистрирует стили и скрипты, специфичные для лендинга.
 */
class LandingAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/landing.css',
        'css/normalize.css',
    ];
    public $js = [
        'js/landing.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}

<?php

namespace app\assets;

use yii\web\AssetBundle;

class AutocompleteAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/vendor/autocomplete/autocomplete.min.css',
    ];

    public $js = [
        'js/vendor/autocomplete/autocomplete.min.js',
    ];
}
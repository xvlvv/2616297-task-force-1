<?php

namespace app\assets;

use yii\base\InvalidConfigException;
use yii\web\AssetBundle;
use yii\web\View;

class YandexMapAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [];

    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];

    public function init(): void
    {
        parent::init();

        $apiKey = $_ENV['YANDEX_GEOCODER_API_KEY'] ?? null;

        if (null === $apiKey) {
            throw new InvalidConfigException('YandexMap API key is required.');
        }

        $this->js[] = "https://api-maps.yandex.ru/2.1/?apikey={$apiKey}&lang=ru_RU";
    }
}
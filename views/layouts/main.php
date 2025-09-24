<?php

declare(strict_types = 1);

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use yii\bootstrap5\Html;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<header class="page-header">
    <nav class="main-nav">
        <?php
        $logoImage = Html::img(['/img/logotype.png'], [
            'class' => 'logo-image',
            'width' => 227,
            'height' => 60,
            'alt' => 'taskforce'
        ]);

        echo Html::a($logoImage, ['/site/index'], ['class' => 'header-logo']);
        ?>
        <?php if (!Yii::$app->user->isGuest): ?>
        <div class="nav-wrapper">
            <ul class="nav-list">
                <li class="list-item <?= 'task/index' === Yii::$app->requestedRoute ? 'list-item--active' : '' ?>">
                    <?= Html::a(
                        'Новое',
                        ['task/index'],
                        ['class' => 'link link--nav']
                    ) ?>
                </li>
                <li class="list-item <?= 'task/something' === Yii::$app->requestedRoute ? 'list-item--active' : '' ?>">
                    <a href="#" class="link link--nav">Мои задания</a>
                </li>
                <?php if (Yii::$app->user->can('publishTask')): ?>
                    <li class="list-item <?= 'task/publish' === Yii::$app->requestedRoute ? 'list-item--active' : '' ?>">
                        <?= Html::a(
                            'Создать задание',
                            ['task/publish'],
                            ['class' => 'link link--nav']
                        ) ?>
                    </li>
                <?php endif ?>
                <li class="list-item <?= 'site/something' === Yii::$app->requestedRoute ? 'list-item--active' : '' ?>">
                    <a href="#" class="link link--nav" >Настройки</a>
                </li>
            </ul>
        </div>
        <?php endif ?>
    </nav>
    <?php if (!Yii::$app->user->isGuest): ?>
    <?php $user = Yii::$app->user->identity->getUser() ?>
    <div class="user-block">
        <a href="#">
            <?= $user->getAvatarPath() ? Html::img(
                $user->getAvatarPath(),
                [
                    'class' => 'user-photo',
                    'width' => 55,
                    'height' => 55,
                    'alt' => 'Аватар'
                ]
            ) : '' ?>
        </a>
        <div class="user-menu">
            <p class="user-name"><?= Html::encode($user->getName()) ?></p>
            <div class="popup-head">
                <ul class="popup-menu">
                    <li class="menu-item">
                        <a href="#" class="link">Настройки</a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="link">Связаться с нами</a>
                    </li>
                    <li class="menu-item">
                        <?= Html::a(
                            'Выход из системы',
                            ['site/logout'],
                            ['class' => 'link']
                        ) ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <?php endif ?>
</header>

<main class="main-content container">
    <?= $content ?>
</main>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

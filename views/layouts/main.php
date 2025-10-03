<?php

declare(strict_types=1);

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
<?php
$this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php
    $this->head() ?>
</head>
<body>
<?php
$this->beginBody() ?>

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
        <?php
        if (!Yii::$app->user->isGuest): ?>
            <div class="nav-wrapper">
                <ul class="nav-list">
                    <li class="list-item <?= 'task/index' === Yii::$app->requestedRoute ? 'list-item--active' : '' ?>">
                        <?= Html::a(
                            'Новое',
                            ['task/index'],
                            ['class' => 'link link--nav']
                        ) ?>
                    </li>
                    <?php
                    if (Yii::$app->user->can('applyTask')): ?>
                        <li class="list-item <?= 'task/my' === Yii::$app->requestedRoute ? 'list-item--active' : '' ?>">
                            <?= Html::a(
                                'Мои задания',
                                ['task/my'],
                                ['class' => 'link link--nav']
                            ) ?>
                        </li>
                    <?php
                    endif ?>
                    <?php
                    if (Yii::$app->user->can('publishTask')): ?>
                        <li class="list-item <?= 'task/publish'
                                                 === Yii::$app->requestedRoute ? 'list-item--active' : '' ?>">
                            <?= Html::a(
                                'Создать задание',
                                ['task/publish'],
                                ['class' => 'link link--nav']
                            ) ?>
                        </li>
                    <?php
                    endif ?>
                    <?php
                    $isInSettingsSection = 'settings/profile' === Yii::$app->requestedRoute
                                           || 'settings/security' === Yii::$app->requestedRoute;
                    ?>
                    <?php
                    if (false === Yii::$app->user?->identity?->getUser()?->isRegisteredWithVk()
                        || Yii::$app->user->can(
                            'applyToTask'
                        )): ?>
                        <li class="list-item <?= $isInSettingsSection ? 'list-item--active' : '' ?>">
                            <?= Html::a(
                                'Настройки',
                                ['settings/profile'],
                                ['class' => 'link link--nav']
                            ) ?>
                        </li>
                    <?php
                    endif ?>
                </ul>
            </div>
        <?php
        endif ?>
    </nav>
    <?php
    if (!Yii::$app->user->isGuest): ?>
        <?php
        $user = Yii::$app->user->identity->getUser() ?>
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
                        <?php
                        if (false === Yii::$app->user?->identity?->getUser()?->isRegisteredWithVk()
                            || Yii::$app->user->can(
                                'applyToTask'
                            )): ?>
                            <li class="menu-item">
                                <?= Html::a(
                                    'Настройки',
                                    ['settings/profile'],
                                    ['class' => 'link']
                                ) ?>
                            </li>
                        <?php
                        endif ?>
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
    <?php
    endif ?>
</header>

<?= $content ?>

<?php
$this->endBody() ?>
</body>
</html>
<?php
$this->endPage() ?>

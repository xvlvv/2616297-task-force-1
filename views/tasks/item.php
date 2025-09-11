<?php
use yii\helpers\Html;

?>

<div class="task-card">
    <div class="header-task">
        <a  href="#" class="link link--block link--big"><?= $model->name ?></a>
        <p class="price price--task"><?= $model->budget ?> ₽</p>
    </div>
    <p class="info-text"><?= Yii::$app->formatter->asRelativeTime($model->createdAt) ?></p>
    <p class="task-text">
        <?= $model->description ?>
    </p>
    <div class="footer-task">
        <p class="info-text town-text"><?= $model->city ?></p>
        <p class="info-text category-text"><?= $model->category ?></p>
        <a href="#" class="button button--black">Смотреть Задание</a>
    </div>
</div>
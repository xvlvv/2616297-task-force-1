<?php

/**
 * @var Model|null $model DTO c данными задания
 */

use yii\base\Model;
use yii\helpers\Html;

?>

<div class="task-card">
    <div class="header-task">
        <?= Html::a(
            Html::encode($model->name),
            ['task/view', 'id' => $model->id],
            ['class' => 'link link--block link--big']
        ) ?>
        <p class="price price--task"><?= Html::encode($model->budget) ?> ₽</p>
    </div>
    <p class="info-text"><?= Yii::$app->formatter->asRelativeTime($model->createdAt) ?></p>
    <p class="task-text">
        <?= Html::encode($model->description) ?>
    </p>
    <div class="footer-task">
        <p class="info-text town-text"><?= Html::encode($model->city) ?></p>
        <p class="info-text category-text"><?= Html::encode($model->category) ?></p>
        <?= Html::a(
            'Смотреть Задание',
            ['task/view', 'id' => $model->id],
            ['class' => 'button button--black']
        ) ?>
    </div>
</div>
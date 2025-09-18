<?php

/**
 * @var ViewTaskDTO $task Данные задания
 */

use Xvlvv\DTO\ViewTaskDTO;
use yii\helpers\Html;

?>
<div class="left-column">
<!--    <pre>-->
<!--        --><?php //var_dump($task); ?>
<!--    </pre>-->


    <div class="head-wrapper">
        <h3 class="head-main"> <?= Html::encode($task->name) ?> </h3>
        <p class="price price--big"><?= Html::encode($task->budget) ?> ₽</p>
    </div>
    <p class="task-description">
        <?= Html::encode($task->description) ?>
    </p>
    <a href="#" class="button button--blue action-btn" data-action="act_response">Откликнуться на задание</a>
    <a href="#" class="button button--orange action-btn" data-action="refusal">Отказаться от задания</a>
    <a href="#" class="button button--pink action-btn" data-action="completion">Завершить задание</a>
    <div class="task-map">
        <img class="map" src="img/map.png"  width="725" height="346" alt="Новый арбат, 23, к. 1">
        <p class="map-address town">Москва</p>
        <p class="map-address">Новый арбат, 23, к. 1</p>
    </div>
    <?php if (!empty($task->responses)): ?>
    <h4 class="head-regular">Отклики на задание</h4>
        <?php foreach ($task->responses as $response): ?>
        <div class="response-card">
            <img class="customer-photo" src="<?= Html::encode($response->avatarPath) ?>" width="146" height="156" alt="Фото заказчиков">
            <div class="feedback-wrapper">
                <?= Html::a(
                    Html::encode($response->workerName),
                    ['user/view', 'id' => $response->id],
                    ['class' => 'link link--block link--big']
                ) ?>
                <div class="response-wrapper">
                    <div class="stars-rating small">
                        <?php
                        $maxRating = 5;
                        $rating = (int) round($response->rating);
                        for ($i = 1; $i <= $maxRating; $i++): ?>
                        <span <?= $rating >= $i ? 'class="fill-star"' : '' ?>>&nbsp;
                        <?php endfor ?>
                    </div>
                    <p class="reviews">
                        <?= Yii::t(
                            'app',
                            '{n, plural, =0{Нет отзывов} =1{1 отзыв} one{# отзыв} few{# отзыва} many{# отзывов} other{# отзывов}}',
                            ['n' => $response->reviewCount]
                        ) ?>
                    </p>
                </div>
                <p class="response-message">
                    <?= Html::encode($response->comment) ?>
                </p>

            </div>
            <div class="feedback-wrapper">
                <p class="info-text"><?= Yii::$app->formatter->asRelativeTime($response->createdAt) ?></p>
                <p class="price price--small"><?= Html::encode($response->price) ?>&nbsp;₽</p>
            </div>
            <div class="button-popup">
                <a href="#" class="button button--blue button--small">Принять</a>
                <a href="#" class="button button--orange button--small">Отказать</a>
            </div>
        </div>
        <?php endforeach ?>
    <?php endif ?>
</div>
<div class="right-column">
    <div class="right-card black info-card">
        <h4 class="head-card">Информация о задании</h4>
        <dl class="black-list">
            <dt>Категория</dt>
            <dd><?= Html::encode($task->category) ?></dd>
            <dt>Дата публикации</dt>
            <dd><?= Yii::$app->formatter->asRelativeTime($task->createdAt) ?></dd>
            <dt>Срок выполнения</dt>
            <dd><?= Yii::$app->formatter->asDatetime($task->endDate, 'd MMMM, H:m') ?></dd>
            <dt>Статус</dt>
            <dd><?= Html::encode($task->status) ?></dd>
        </dl>
    </div>
    <div class="right-card white file-card">
        <h4 class="head-card">Файлы задания</h4>
        <ul class="enumeration-list">
            <li class="enumeration-item">
                <a href="#" class="link link--block link--clip">my_picture.jpg</a>
                <p class="file-size">356 Кб</p>
            </li>
            <li class="enumeration-item">
                <a href="#" class="link link--block link--clip">information.docx</a>
                <p class="file-size">12 Кб</p>
            </li>
        </ul>
    </div>
</div>
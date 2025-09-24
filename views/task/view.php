<?php

declare(strict_types = 1);

/**
 * @var ViewTaskDTO $task Данные задания
 */

use app\widgets\RatingWidget;
use Xvlvv\DTO\ViewTaskDTO;
use Xvlvv\Enums\Action;
use yii\helpers\Html;

?>
<div class="left-column">
    <div class="head-wrapper">
        <h3 class="head-main"> <?= Html::encode($task->name) ?> </h3>
        <p class="price price--big"><?= Html::encode($task->budget) ?> ₽</p>
    </div>
    <p class="task-description">
        <?= Html::encode($task->description) ?>
    </p>
    <?php
    foreach ($task->availableActions as $action) {
        echo Html::button(
            $action->getName(),
            [
                'class' => "button button--{$action->getInternalName()} action-btn",
                'data-action' => $action->getInternalName()
            ]
        );
    } ?>
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
                    <?= RatingWidget::widget(['rating' => $response->rating, 'wrapperClass' => 'small']) ?>
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
                <a href="#"
                   class="button button--<?= Action::APPLY->getActionObject()->getInternalName() ?> button--small">Принять</a>
                <a href="#"
                   class="button button--<?= Action::FAIL->getActionObject()->getInternalName() ?> button--small">Отказать</a>
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
    <?php if (!empty($task->files)): ?>
    <div class="right-card white file-card">
        <h4 class="head-card">Файлы задания</h4>
        <ul class="enumeration-list">
            <?php foreach ($task->files as $file): ?>
            <li class="enumeration-item">
                <a href="<?= $file->path ?>" class="link link--block link--clip"><?= Html::encode($file->original_name) ?></a>
                <p class="file-size"><?= Yii::$app->formatter->asShortSize(
                        filesize($file->path),
                        options: [
                            NumberFormatter::MAX_FRACTION_DIGITS => 0,
                        ]
                    ) ?></p>
            </li>
            <?php endforeach ?>
        </ul>
    </div>
    <?php endif ?>
</div>
<section class="pop-up pop-up--<?= Action::FAIL->getActionObject()->getInternalName() ?> pop-up--close">
    <div class="pop-up--wrapper">
        <h4>Отказ от задания</h4>
        <p class="pop-up-text">
            <b>Внимание!</b><br>
            Вы собираетесь отказаться от выполнения этого задания.<br>
            Это действие плохо скажется на вашем рейтинге и увеличит счетчик проваленных заданий.
        </p>
        <a class="button button--pop-up button--orange">Отказаться</a>
        <div class="button-container">
            <button class="button--close" type="button">Закрыть окно</button>
        </div>
    </div>
</section>
<section class="pop-up pop-up--<?= Action::COMPLETE->getActionObject()->getInternalName() ?> pop-up--close">
    <div class="pop-up--wrapper">
        <h4>Завершение задания</h4>
        <p class="pop-up-text">
            Вы собираетесь отметить это задание как выполненное.
            Пожалуйста, оставьте отзыв об исполнителе и отметьте отдельно, если возникли проблемы.
        </p>
        <div class="completion-form pop-up--form regular-form">
            <form>
                <div class="form-group">
                    <label class="control-label" for="completion-comment">Ваш комментарий</label>
                    <textarea id="completion-comment"></textarea>
                </div>
                <p class="completion-head control-label">Оценка работы</p>
                <div class="stars-rating big active-stars"><span>&nbsp;</span><span>&nbsp;</span><span>&nbsp;</span><span>&nbsp;</span><span>&nbsp;</span></div>
                <input type="submit" class="button button--pop-up button--blue" value="Завершить">
            </form>
        </div>
        <div class="button-container">
            <button class="button--close" type="button">Закрыть окно</button>
        </div>
    </div>
</section>
<section class="pop-up pop-up--<?= Action::APPLY->getActionObject()->getInternalName() ?> pop-up--close">
    <div class="pop-up--wrapper">
        <h4>Добавление отклика к заданию</h4>
        <p class="pop-up-text">
            Вы собираетесь оставить свой отклик к этому заданию.
            Пожалуйста, укажите стоимость работы и добавьте комментарий, если необходимо.
        </p>
        <div class="addition-form pop-up--form regular-form">
            <form>
                <div class="form-group">
                    <label class="control-label" for="addition-comment">Ваш комментарий</label>
                    <textarea id="addition-comment"></textarea>
                </div>
                <div class="form-group">
                    <label class="control-label" for="addition-price">Стоимость</label>
                    <input id="addition-price" type="text">
                </div>
                <input type="submit" class="button button--pop-up button--blue" value="Завершить">
            </form>
        </div>
        <div class="button-container">
            <button class="button--close" type="button">Закрыть окно</button>
        </div>
    </div>
</section>
<div class="overlay"></div>
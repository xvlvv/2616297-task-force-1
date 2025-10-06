<?php

declare(strict_types=1);

/**
 * @var ViewUserDTO $user Данные о пользователе
 */

use app\widgets\RatingWidget;
use Xvlvv\DTO\ViewUserDTO;
use yii\helpers\Html;

?>

<main class="main-content container">
    <div class="left-column">
        <h3 class="head-main"><?= Html::encode($user->name) ?></h3>
        <div class="user-card">
            <div class="photo-rate">
                <img class="card-photo" src="<?= Html::encode($user->avatarPath) ?>" width="191" height="190"
                     alt="Фото пользователя">
                <div class="card-rate">
                    <?= RatingWidget::widget(['rating' => $user->rating, 'wrapperClass' => 'big']) ?>
                    <span class="current-rate"><?= Html::encode($user->rating) ?></span>
                </div>
            </div>
            <p class="user-description">
                <?= Html::encode($user->description) ?>
            </p>
        </div>
        <div class="specialization-bio">
            <div class="specialization">
                <p class="head-info">Специализации</p>
                <?php
                if (!empty($user->specializations)): ?>
                    <ul class="special-list">
                        <?php
                        foreach ($user->specializations as $specialization): ?>
                            <li class="special-item">
                                <a href="#" class="link link--regular"><?= Html::encode($specialization->name) ?></a>
                            </li>
                        <?php
                        endforeach ?>
                    </ul>
                <?php
                endif ?>
            </div>
            <div class="bio">
                <p class="head-info">Био</p>
                <p class="bio-info">
                    <?= Html::encode($user->bio) ?>
            </div>
        </div>
        <?php
        if (!empty($user->reviews)): ?>
            <h4 class="head-regular">Отзывы заказчиков</h4>
            <?php
            foreach ($user->reviews as $review): ?>
                <div class="response-card">
                    <img class="customer-photo" src="<?= Html::encode($review->avatarPath) ?>" width="120" height="127"
                         alt="Фото заказчиков">
                    <div class="feedback-wrapper">
                        <p class="feedback">«<?= Html::encode($review->comment) ?>»</p>
                        <p class="task">
                            Задание «<?= Html::a(
                                Html::encode($review->taskName),
                                ['task/view', 'id' => $review->taskId],
                                ['class' => 'link link--small']
                            ) ?>» выполнено
                        </p>
                    </div>
                    <div class="feedback-wrapper">

                        <?= RatingWidget::widget(['rating' => $review->rating, 'wrapperClass' => 'small']) ?>

                        <p class="info-text">
                            <?= Yii::$app->formatter->asRelativeTime($review->createdAt) ?>
                        </p>
                    </div>
                </div>
            <?php
            endforeach ?>
        <?php
        endif ?>
    </div>
    <div class="right-column">
        <div class="right-card black">
            <h4 class="head-card">Статистика исполнителя</h4>
            <dl class="black-list">
                <dt>Всего заказов</dt>
                <dd><?= Html::encode($user->completedTasks) ?> выполнено, <?= Html::encode($user->failedTasks) ?>
                    провалено
                </dd>
                <dt>Место в рейтинге</dt>
                <dd><?= Html::encode($user->ratingPlacement) ?> место</dd>
                <dt>Дата регистрации</dt>
                <dd><?= Yii::$app->formatter->asRelativeTime($user->createdAt) ?></dd>
                <dt>Статус</dt>
                <dd>Открыт для новых заказов</dd>
            </dl>
        </div>
        <?php
        if (true === $user->showContacts): ?>
            <div class="right-card white">
                <h4 class="head-card">Контакты</h4>
                <ul class="enumeration-list">
                    <li class="enumeration-item">
                        <a href="#" class="link link--block link--phone"><?= Html::encode($user->phoneNumber) ?></a>
                    </li>
                    <li class="enumeration-item">
                        <a href="#" class="link link--block link--email"><?= Html::encode($user->email) ?></a>
                    </li>
                    <li class="enumeration-item">
                        <a href="#" class="link link--block link--tg">@<?= Html::encode($user->telegramUsername) ?></a>
                    </li>
                </ul>
            </div>
        <?php
        endif ?>
    </div>
</main>

<?php

declare(strict_types=1);

/**
 * @var ViewUserDTO $dto Данные о пользователе
 */

use Xvlvv\DTO\ViewUserDTO;
use yii\helpers\Html;

?>

<div class="left-column">
    <h3 class="head-main"><?= Html::encode($dto->name) ?></h3>
    <div class="user-card">
        <div class="photo-rate">
            <img class="card-photo" src="<?= Html::encode($dto->avatarPath) ?>" width="191" height="190" alt="Фото пользователя">
            <div class="card-rate">
                <div class="stars-rating big">
                    <?php
                    $maxRating = 5;
                    $rating = (int) round($dto->rating);
                    for ($i = 1; $i <= $maxRating; $i++): ?>
                        <span <?= $rating >= $i ? 'class="fill-star"' : '' ?>>&nbsp;</span>
                    <?php endfor ?>
                </div>
                <span class="current-rate"><?= Html::encode($dto->rating) ?></span>
            </div>
        </div>
        <p class="user-description">
            <?= Html::encode($dto->description) ?>
        </p>
    </div>
    <div class="specialization-bio">
        <div class="specialization">
            <p class="head-info">Специализации</p>
            <?php if (!empty($dto->specializations)): ?>
            <ul class="special-list">
                <?php foreach ($dto->specializations as $specialization): ?>
                <li class="special-item">
                    <a href="#" class="link link--regular"><?= Html::encode($specialization->name) ?></a>
                </li>
                <?php endforeach ?>
            </ul>
            <?php endif ?>
        </div>
        <div class="bio">
            <p class="head-info">Био</p>
            <p class="bio-info">
                <?= Html::encode($dto->bio) ?>
        </div>
    </div>
    <?php if (!empty($dto->reviews)): ?>
    <h4 class="head-regular">Отзывы заказчиков</h4>
        <?php foreach ($dto->reviews as $review): ?>
        <div class="response-card">
            <img class="customer-photo" src="<?= Html::encode($review->avatarPath) ?>" width="120" height="127" alt="Фото заказчиков">
            <div class="feedback-wrapper">
                <p class="feedback">«<?= Html::encode($review->comment) ?>»</p>
                <p class="task">Задание «<a href="#" class="link link--small"><?= Html::encode($review->taskName) ?></a>» выполнено</p>
            </div>
            <div class="feedback-wrapper">

                <div class="stars-rating small">
                    <?php
                    $maxRating = 5;
                    for ($i = 1; $i <= $maxRating; $i++): ?>
                    <span <?= $review->rating >= $i ? 'class="fill-star"' : '' ?>>&nbsp;</span>
                    <?php endfor ?>

                </div>
                <p class="info-text">
                    <?= Yii::$app->formatter->asRelativeTime($review->createdAt) ?>
                </p>
            </div>
        </div>
        <?php endforeach ?>
    <?php endif ?>
</div>
<div class="right-column">
    <div class="right-card black">
        <h4 class="head-card">Статистика исполнителя</h4>
        <dl class="black-list">
            <dt>Всего заказов</dt>
            <dd><?= Html::encode($dto->completedTasks) ?> выполнено, <?= Html::encode($dto->failedTasks) ?> провалено</dd>
            <dt>Место в рейтинге</dt>
            <dd><?= Html::encode($dto->ratingPlacement) ?> место</dd>
            <dt>Дата регистрации</dt>
            <dd><?= Yii::$app->formatter->asRelativeTime($dto->createdAt) ?></dd>
            <dt>Статус</dt>
            <dd>Открыт для новых заказов</dd>
        </dl>
    </div>
    <?php if (true === $dto->showContacts): ?>
    <div class="right-card white">
        <h4 class="head-card">Контакты</h4>
        <ul class="enumeration-list">
            <li class="enumeration-item">
                <a href="#" class="link link--block link--phone"><?= Html::encode($dto->phoneNumber) ?></a>
            </li>
            <li class="enumeration-item">
                <a href="#" class="link link--block link--email"><?= Html::encode($dto->email) ?></a>
            </li>
            <li class="enumeration-item">
                <a href="#" class="link link--block link--tg">@<?= Html::encode($dto->telegramUsername) ?></a>
            </li>
        </ul>
    </div>
    <?php endif ?>
</div>

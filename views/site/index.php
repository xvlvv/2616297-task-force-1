<?php

declare(strict_types=1);

/**
 * @var LoginForm $loginForm Форма авторизации на сайте
 */

use app\models\LoginForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<main>
    <div class="landing-container">
        <div class="landing-top">
            <h1>Работа для всех.<br>
                Найди исполнителя на любую задачу.</h1>
            <p>Сломался кран на кухне? Надо отправить документы? Нет времени самому гулять с собакой?
                У нас вы быстро найдёте исполнителя для любой жизненной ситуации?<br>
                Быстро, безопасно и с гарантией. Просто, как раз, два, три. </p>
            <?= Html::a(
                Html::encode('Создать аккаунт'),
                ['register'],
                ['class' => 'button']
            ) ?>
        </div>
        <div class="landing-center">
            <div class="landing-instruction">
                <div class="landing-instruction-step">
                    <div class="instruction-circle circle-request"></div>
                    <div class="instruction-description">
                        <h3>Публикация заявки</h3>
                        <p>Создайте новую заявку.</p>
                        <p>Опишите в ней все детали
                            и стоимость работы.</p>
                    </div>
                </div>
                <div class="landing-instruction-step">
                    <div class="instruction-circle  circle-choice"></div>
                    <div class="instruction-description">
                        <h3>Выбор исполнителя</h3>
                        <p>Получайте отклики от мастеров.</p>
                        <p>Выберите подходящего<br>
                            вам исполнителя.</p>
                    </div>
                </div>
                <div class="landing-instruction-step">
                    <div class="instruction-circle  circle-discussion"></div>
                    <div class="instruction-description">
                        <h3>Обсуждение деталей</h3>
                        <p>Обсудите все детали работы<br>
                            в нашем внутреннем чате.</p>
                    </div>
                </div>
                <div class="landing-instruction-step">
                    <div class="instruction-circle circle-payment"></div>
                    <div class="instruction-description">
                        <h3>Оплата&nbsp;работы</h3>
                        <p>По завершении работы оплатите
                            услугу и закройте задание</p>
                    </div>
                </div>
            </div>
            <div class="landing-notice">
                <div class="landing-notice-card card-executor">
                    <h3>Исполнителям</h3>
                    <ul class="notice-card-list">
                        <li>
                            Большой выбор заданий
                        </li>
                        <li>
                            Работайте где удобно
                        </li>
                        <li>
                            Свободный график
                        </li>
                        <li>
                            Удалённая работа
                        </li>
                        <li>
                            Гарантия оплаты
                        </li>
                    </ul>
                </div>
                <div class="landing-notice-card card-customer">
                    <h3>Заказчикам</h3>
                    <ul class="notice-card-list">
                        <li>
                            Исполнители на любую задачу
                        </li>
                        <li>
                            Достоверные отзывы
                        </li>
                        <li>
                            Оплата по факту работы
                        </li>
                        <li>
                            Экономия времени и денег
                        </li>
                        <li>
                            Выгодные цены
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</main>
<footer class="page-footer">
    <div class="main-container page-footer__container">
        <div class="page-footer__info">
            <p class="page-footer__info-copyright">
                © 2021, ООО «ТаскФорс»
                Все права защищены
            </p>
            <p class="page-footer__info-use">
                «TaskForce» — это сервис для поиска исполнителей на разовые задачи.
                mail@taskforce.com
            </p>
        </div>
        <div class="page-footer__links">
            <ul class="links__list">
                <li class="links__item">
                    <a href="">Задания</a>
                </li>
                <li class="links__item">
                    <a href="">Мой профиль</a>
                </li>
                <li class="links__item">
                    <a href="">Исполнители</a>
                </li>
                <li class="links__item">
                    <a href="">Регистрация</a>
                </li>
                <li class="links__item">
                    <a href="">Создать задание</a>
                </li>
                <li class="links__item">
                    <a href="">Справка</a>
                </li>
            </ul>
        </div>
        <div class="page-footer__copyright">
            <a href="https://htmlacademy.ru">
                <img class="copyright-logo"
                     src="./img/academy-logo.png"
                     width="185" height="63"
                     alt="Логотип HTML Academy">
            </a>
        </div>
    </div>
</footer>
<section class="modal enter-form form-modal" id="enter-form">
    <h2>Вход на сайт</h2>

    <?php
    $form = ActiveForm::begin([
        'id' => 'login-form',
        'action' => ['site/index'],
        'enableAjaxValidation' => true,
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'labelOptions' => ['class' => 'form-modal-description'],
            'inputOptions' => ['class' => 'input input-middle'],
        ]
    ]); ?>

    <?= $form->field($loginForm, 'email')->textInput([
        'type' => 'email',
    ]) ?>

    <?= $form->field($loginForm, 'password')->passwordInput() ?>

    <?php
    if (Yii::$app->authClientCollection->hasClient('vk-id')): ?>

        <?= Html::a(
            '<span class="vk-icon"></span><span>Войти с VK ID</span>',
            ['/oauth/redirect'],
            [
                'class' => 'button button--vk',
                'title' => 'Войти с VK ID',
            ]
        ) ?>

    <?php
    endif ?>

    <?= Html::submitButton('Войти', ['class' => 'button']) ?>

    <?php
    ActiveForm::end(); ?>

    <?= Html::button('Закрыть', ['class' => 'form-modal-close']) ?>
</section>
<div class="overlay"></div>
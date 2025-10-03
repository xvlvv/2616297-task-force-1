<?php

declare(strict_types=1);

/**
 * @var ViewTaskDTO $task Данные задания
 * @var ApplyForm $applyForm Форма отклика на задание
 * @var CompleteForm $completeForm Форма завершения задания
 */

use app\models\ApplyForm;
use app\models\CompleteForm;
use app\widgets\RatingWidget;
use Xvlvv\DTO\ViewTaskDTO;
use Xvlvv\Enums\Action;
use Xvlvv\Enums\Status;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\assets\YandexMapAsset;

YandexMapAsset::register($this);

?>
    <main class="main-content container">
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
            <?php
            if (null !== $task->coordinates): ?>
                <div class="task-map">
                    <div id="map-container"
                         style="width: 100%; height: 346px;"
                         data-latitude="<?= $task->coordinates->latitude ?>"
                         data-longitude="<?= $task->coordinates->longitude ?>">
                    </div>
                    <p class="map-address town"><?= Html::encode($task->cityName) ?></p>
                    <?= Html::tag('p', Html::encode($task->additionalInfo), ['class' => 'map-address']) ?>
                </div>
            <?php
            endif ?>
            <?php
            if (!empty($task->responses)): ?>
                <h4 class="head-regular">Отклики на задание</h4>
                <?php
                foreach ($task->responses as $response): ?>
                    <div class="response-card">
                        <?= Html::tag(
                            'img',
                            Html::encode($response->avatarPath),
                            [
                                'class' => 'customer-photo',
                                'width' => '146',
                                'height' => '156',
                                'alt' => 'Фото заказчиков',
                                'src' => $response->avatarPath
                            ]
                        ) ?>
                        <div class="feedback-wrapper">
                            <?= Html::a(
                                Html::encode($response->workerName),
                                ['user/view', 'id' => $response->userId],
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
                        <?php
                        if (
                            Yii::$app->user->can('manageTaskResponses', ['taskId' => $task->id])
                            && !$response->isRejected
                            && $task->status === Status::NEW
                        ): ?>
                            <div class="button-popup">
                                <?= Html::a(
                                    'Принять',
                                    ['task/start', 'id' => $response->id],
                                    ['class' => 'button button--blue button--small']
                                ) ?>
                                <?= Html::a(
                                    'Отказать',
                                    ['task/reject-response', 'id' => $response->id],
                                    ['class' => 'button button--orange button--small']
                                ) ?>
                            </div>
                        <?php
                        endif ?>
                    </div>
                <?php
                endforeach ?>
            <?php
            endif ?>
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
                    <dd><?= Html::encode($task->status === Status::NEW ? 'Открыт для новых заказов' : 'Занят') ?></dd>
                </dl>
            </div>
            <?php
            if (!empty($task->files)): ?>
                <div class="right-card white file-card">
                    <h4 class="head-card">Файлы задания</h4>
                    <ul class="enumeration-list">
                        <?php
                        foreach ($task->files as $file): ?>
                            <li class="enumeration-item">
                                <a href="<?= $file->path ?>" class="link link--block link--clip"><?= Html::encode(
                                        $file->original_name
                                    ) ?></a>
                                <p class="file-size"><?= Yii::$app->formatter->asShortSize(
                                        filesize($file->path),
                                        options: [
                                            NumberFormatter::MAX_FRACTION_DIGITS => 0,
                                        ]
                                    ) ?></p>
                            </li>
                        <?php
                        endforeach ?>
                    </ul>
                </div>
            <?php
            endif ?>
        </div>
        <section class="pop-up pop-up--<?= Action::FAIL->getActionObject()->getInternalName() ?> pop-up--close">
            <div class="pop-up--wrapper">
                <h4>Отказ от задания</h4>
                <p class="pop-up-text">
                    <b>Внимание!</b><br>
                    Вы собираетесь отказаться от выполнения этого задания.<br>
                    Это действие плохо скажется на вашем рейтинге и увеличит счетчик проваленных заданий.
                </p>
                <?= Html::a(
                    Action::FAIL->getActionObject()->getName(),
                    ['task/fail', 'id' => $task->id],
                    ['class' => 'button button--pop-up button--orange']
                ) ?>
                <div class="button-container">
                    <?= Html::button('Закрыть окно', ['class' => 'button--close']) ?>
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
                    <?php
                    $form = ActiveForm::begin([
                        'method' => 'post',
                        'action' => ['/task/complete', 'id' => $task->id],
                        'enableAjaxValidation' => true,
                    ]) ?>

                    <?= $form->field($completeForm, 'comment')->textarea() ?>

                    <p class="completion-head control-label">Оценка работы</p>

                    <?= $form->field($completeForm, 'rating', [
                        'template' => '{input}{error}',
                        'options' => ['class' => 'stars-rating-form'],
                    ])->radioList(
                        [5 => '', 4 => '', 3 => '', 2 => '', 1 => ''],
                        [
                            'item' => function ($index, $label, $name, $checked, $value) {
                                $return = '<input class="star-input" id="star-' . $value . '" type="radio" name="'
                                          . $name
                                          . '" value="' . $value . '" ' . ($checked ? 'checked' : '') . '>';
                                $return .= '<label class="star-label" for="star-' . $value . '"></label>';
                                return $return;
                            },
                            'encode' => false,
                        ]
                    )->label(false)
                    ?>

                    <?= Html::submitInput('Завершить', ['class' => 'button button--pop-up button--blue']) ?>

                    <?php
                    ActiveForm::end() ?>
                </div>
                <div class="button-container">
                    <?= Html::button('Закрыть окно', ['class' => 'button--close']) ?>
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
                    <?php
                    $form = ActiveForm::begin([
                        'method' => 'post',
                        'action' => ['/task/apply', 'id' => $task->id],
                        'enableAjaxValidation' => true,
                    ]) ?>

                    <?= $form->field($applyForm, 'description')->textarea() ?>

                    <?= $form->field($applyForm, 'price')->textInput() ?>

                    <?= Html::submitInput('Завершить', ['class' => 'button button--pop-up button--blue']) ?>

                    <?php
                    ActiveForm::end() ?>
                </div>
                <div class="button-container">
                    <?= Html::button('Закрыть окно', ['class' => 'button--close']) ?>
                </div>
            </div>
        </section>
        <section class="pop-up pop-up--<?= Action::CANCEL->getActionObject()->getInternalName() ?> pop-up--close">
            <div class="pop-up--wrapper">
                <h4>Отменить задание</h4>
                <p class="pop-up-text">
                    Вы собираетесь отменить опубликованное задание.
                    Пожалуйста, подтвердите действие.
                </p>
                <?= Html::a(
                    Action::CANCEL->getActionObject()->getName(),
                    ['task/cancel', 'id' => $task->id],
                    ['class' => 'button button--pop-up button--red']
                ) ?>
                <div class="button-container">
                    <?= Html::button('Закрыть окно', ['class' => 'button--close']) ?>
                </div>
            </div>
        </section>
        <div class="overlay"></div>
    </main>

<?php
$js = <<<JS

ymaps.ready(init);

function init() {
    
    const mapElement = document.getElementById('map-container');
    
    if (!mapElement) {
        return;
    }

    const latitude = parseFloat(mapElement.dataset.latitude);
    const longitude = parseFloat(mapElement.dataset.longitude);
    
    if (isNaN(latitude) || isNaN(longitude)) {
        return;
    }
    
    const centerCoordinates = [latitude, longitude];

    const myMap = new ymaps.Map('map-container', {
            center: centerCoordinates,
            zoom: 15
    });
    
    const location = new ymaps.Placemark(centerCoordinates);
    myMap.geoObjects.add(location);
}

JS;

$this->registerJs($js, \yii\web\View::POS_END);
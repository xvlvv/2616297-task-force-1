<?php

declare(strict_types = 1);

use app\models\PublishForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var PublishForm $formModel Модель формы публикации задания
 * @var array $categories Список категорий [идентификатор => название]
 */

?>
<div class="add-task-form regular-form center-block">
    <?php
    $form = ActiveForm::begin([
        'method' => 'post',
        'action' => ['/publish'],
    ]) ?>

    <h3 class="head-main head-main">Публикация нового задания</h3>
    <div class="form-group">
        <?= $form->field($formModel, 'name')->textInput() ?>
    </div>

    <div class="form-group">
        <?= $form->field($formModel, 'description')->textarea() ?>
    </div>

    <div class="form-group">
        <?= $form->field($formModel, 'categoryId')->dropDownList($categories) ?>
    </div>

<!--    <div class="form-group">-->
<!--        <label class="control-label" for="location">Локация</label>-->
<!--        <input class="location-icon" id="location" type="text">-->
<!--        <span class="help-block">Error description is here</span>-->
<!--    </div>-->

    <div class="half-wrapper">
        <div class="form-group">
            <?= $form->field($formModel, 'budget')->textInput() ?>
        </div>
        <div class="form-group">
            <?= $form->field($formModel, 'endDate')->input('date') ?>
        </div>
    </div>

    <?= $form->field($formModel, 'files[]', [
        'template' => "
        <p class=\"form-label\">{label}</p>
        <label class=\"new-file\">
            <span>Добавить новый файл</span>
            {input}
        </label>\n
        {hint}\n
        {error}
    ",
    ])->fileInput([
        'multiple' => true,
        'style' => 'display: none;',
    ]);
    ?>

    <?= Html::submitInput('Опубликовать', ['class' => 'button button--blue']) ?>

    <?php ActiveForm::end() ?>
</div>
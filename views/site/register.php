<?php

/**
 * @var User $formModel Форма регистрации
 * @var array $cities Список городов
 */

use app\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="center-block">
    <div class="registration-form regular-form">
        <?php

        $form = ActiveForm::begin([
            'method' => 'post',
            'action' => ['/register'],
        ]) ?>

        <h3 class="head-main head-task">Регистрация нового пользователя</h3>
        <?= $form->field($formModel, 'name')->textInput() ?>

        <div class="half-wrapper">
            <?= $form->field($formModel, 'email')->textInput() ?>
            <?= $form->field($formModel, 'cityId')->dropDownList($cities) ?>
        </div>

        <div class="half-wrapper">
            <?= $form->field($formModel, 'password')->textInput() ?>
        </div>

        <div class="half-wrapper">
            <?= $form->field($formModel, 'passwordRepeat')->textInput() ?>
        </div>

        <div class="form-group">
            <?= $form->field(
                $formModel,
                'isWorker',
                [
                    'options' => [
                        'class' => 'control-label checkbox-label'
                    ],
                ]
            )->checkbox()
            ?>
        </div>

        <?= Html::submitInput('Создать аккаунт', ['class' => 'button button--blue']) ?>

        <?php ActiveForm::end() ?>
    </div>
</div>
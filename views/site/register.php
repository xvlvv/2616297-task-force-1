<?php

declare(strict_types = 1);

/**
 * @var User $formModel Форма регистрации
 * @var array $cities Список городов
 * @var bool $isRegisterWithVK Флаг что пользователь регистрируется через VK ID
 */

use app\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<main class="main-content container">
    <div class="center-block">
        <div class="registration-form regular-form">
            <?php

            $form = ActiveForm::begin([
                'method' => 'post',
                'action' => ['/register'],
            ]) ?>

            <h3 class="head-main head-task">Регистрация нового пользователя</h3>
            <?= $form->field($formModel, 'name')->textInput(['readOnly' => $isRegisterWithVK]) ?>

            <div class="half-wrapper">
                <?= $form->field($formModel, 'email')->textInput(['readOnly' => $isRegisterWithVK]) ?>
                <?= $form->field($formModel, 'cityId')->dropDownList($cities) ?>
            </div>

            <?php if (!$isRegisterWithVK): ?>
            <div class="half-wrapper">
                <?= $form->field($formModel, 'password')->textInput() ?>
            </div>

            <div class="half-wrapper">
                <?= $form->field($formModel, 'passwordRepeat')->textInput() ?>
            </div>
            <?php endif ?>

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

            <?php if (!$isRegisterWithVK && Yii::$app->authClientCollection->hasClient('vk-id')): ?>

                <?= Html::a(
                    '<span class="vk-icon"></span><span>Войти с VK ID</span>',
                    ['/oauth/redirect'],
                    [
                        'class' => 'button button--vk',
                        'title' => 'Войти с VK ID',
                    ]
                ) ?>

            <?php endif ?>

            <?= Html::submitInput('Создать аккаунт', ['class' => 'button button--blue']) ?>

            <?php ActiveForm::end() ?>
        </div>
    </div>
</main>
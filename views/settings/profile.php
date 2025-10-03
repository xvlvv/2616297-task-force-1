<?php
/**
 * @var View $this
 * @var ProfileEditForm $model
 * @var User $user
 * @var array $categories
 */

use app\models\ProfileEditForm;
use Xvlvv\Entity\User;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

?>
<main class="main-content main-content--left container">
    <?= $this->render('_menu') ?>
    <div class="my-profile-form">
        <?php
        if (Yii::$app->session->hasFlash('success')): ?>
            <div class="alert alert--success">
                <?= Yii::$app->session->getFlash('success') ?>
            </div>
        <?php
        endif; ?>

        <?php
        $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
        <h3 class="head-main head-regular">Мой профиль</h3>

        <div class="photo-editing">
            <div>
                <?php
                if (null !== $user->getAvatarPath()): ?>
                    <p class="form-label">Аватар</p>
                    <?= Html::img($user->getAvatarPath(), ['class' => 'avatar-preview', 'width' => 83, 'height' => 83]
                    ) ?>
                <?php
                endif ?>
            </div>
            <?= $form->field($model, 'avatarFile', ['template' => '{input}{label}{error}'])
                ->fileInput(['id' => 'button-input', 'hidden' => true])
                ->label('Сменить аватар', ['class' => 'button button--black', 'for' => 'button-input']) ?>
        </div>

        <?= $form->field($model, 'name')->textInput() ?>

        <div class="half-wrapper">
            <?= $form->field($model, 'email')->input('email') ?>
            <?= $form->field($model, 'birthday')->input('date') ?>
        </div>

        <div class="half-wrapper">
            <?= $form->field($model, 'phone')->input('tel') ?>
            <?= $form->field($model, 'telegram')->textInput() ?>
        </div>

        <?= $form->field($model, 'bio')->textarea() ?>

        <?= $form->field($model, 'specializations')->checkboxList($categories, [
            'item' => function ($index, $label, $name, $checked, $value) {
                return '<label class="control-label">'
                       . Html::checkbox($name, $checked, ['value' => $value, 'id' => 'spec-' . $value])
                       . $label
                       . '</label>';
            },
            'class' => 'checkbox-profile'
        ]) ?>

        <?= Html::submitInput('Сохранить', ['class' => 'button button--blue']) ?>
        <?php
        ActiveForm::end(); ?>
    </div>
</main>
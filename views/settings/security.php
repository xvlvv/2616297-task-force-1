<?php
/**
 * @var View $this
 * @var SecurityForm $model
 */

use app\models\SecurityForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->title = 'Безопасность';
?>
<main class="main-content main-content--left container">
    <?= $this->render('_menu') ?>
    <div class="my-profile-form">
        <?php $form = ActiveForm::begin(); ?>
        <h3 class="head-main head-regular">Смена пароля</h3>

        <?php if (Yii::$app->session->hasFlash('success')): ?>
            <div class="alert alert--success">
                <?= Yii::$app->session->getFlash('success') ?>
            </div>
        <?php endif; ?>

        <?= $form->field($model, 'currentPassword')->passwordInput() ?>
        <?= $form->field($model, 'newPassword')->passwordInput() ?>
        <?= $form->field($model, 'passwordRepeat')->passwordInput() ?>

        <?= Html::submitInput('Сохранить', ['class' => 'button button--blue']) ?>
        <?php ActiveForm::end(); ?>
    </div>
</main>
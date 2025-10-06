<?php
/**
 * @var View $this
 * Partial view для бокового меню настроек.
 */

use yii\helpers\Html;
use yii\web\View;

$currentAction = Yii::$app->controller->action->id;
?>
<div class="left-menu left-menu--edit">
    <h3 class="head-main head-task">Настройки</h3>
    <ul class="side-menu-list">

        <?php
        if (Yii::$app->user->can('applyToTask')): ?>
            <li class="side-menu-item <?= ($currentAction === 'profile') ? 'side-menu-item--active' : '' ?>">
                <?= Html::a('Мой профиль', ['/settings/profile'], ['class' => 'link link--nav']) ?>
            </li>
        <?php
        endif; ?>

        <li class="side-menu-item <?= ($currentAction === 'security') ? 'side-menu-item--active' : '' ?>">
            <?= Html::a('Безопасность', ['/settings/security'], ['class' => 'link link--nav']) ?>
        </li>

    </ul>
</div>
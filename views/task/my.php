<?php
/**
 * @var View $this
 * @var TaskListItemDTO[] $tasks
 * @var string $activeTab
 * @var array $tabTitles
 */

use Xvlvv\DTO\TaskListItemDTO;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

?>
<main class="main-content container">
    <div class="left-menu">
        <h3 class="head-main head-task">Мои задания</h3>
        <ul class="side-menu-list">
            <?php foreach ($tabTitles as $tabKey => $tabTitle): ?>
                <li class="side-menu-item <?= ($activeTab === $tabKey) ? 'side-menu-item--active' : '' ?>">
                    <a href="<?= Url::to(['/task/my', 'tab' => $tabKey]) ?>" class="link link--nav"><?= $tabTitle ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="left-column left-column--task">
        <h3 class="head-main head-regular"><?= $tabTitles[$activeTab] ?? 'Задания' ?></h3>

        <?php if (empty($tasks)): ?>
            <p>Заданий с таким статусом не найдено.</p>
        <?php else: ?>
            <?php foreach ($tasks as $task): ?>
                <div class="task-card">
                    <div class="header-task">
                        <a href="<?= Url::to(['/task/view', 'id' => $task->id]) ?>" class="link link--block link--big">
                            <?= Html::encode($task->name) ?>
                        </a>
                        <p class="price price--task"><?= Html::encode($task->budget) ?> ₽</p>
                    </div>
                    <p class="info-text"><span class="current-time"><?= Yii::$app->formatter->asRelativeTime($task->createdAt) ?></span></p>
                    <p class="task-text"><?= Html::encode($task->description) ?></p>
                    <div class="footer-task">
                        <p class="info-text town-text"><?= Html::encode($task->cityName) ?></p>
                        <p class="info-text category-text"><?= Html::encode($task->categoryName) ?></p>
                        <a href="<?= Url::to(['/task/view', 'id' => $task->id]) ?>" class="button button--black">Смотреть Задание</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>
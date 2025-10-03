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
            <?= yii\widgets\ListView::widget([
                'dataProvider' => $tasks,
                'itemView' => 'item',
                'summary' => '',
                'emptyText' => 'Новых заданий по заданным критериям не найдено',
            ]);
            ?>
        <?php endif; ?>
    </div>
</main>
<?php

declare(strict_types=1);

/**
 * @var array $tasks Массив DTO заданий
 * @var TaskSearch $model Модель фильтра
 * @var array $categories Массив категорий новых заданий
 * @var Pagination $pagination Yii2 объект пагинации
 */

use app\models\TaskSearch;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

?>
<main class="main-content container">
    <div class="left-column">
        <h3 class="head-main head-task">Новые задания</h3>
        <?= yii\widgets\ListView::widget([
            'dataProvider' => $tasks,
            'itemView' => 'item',
            'summary' => '',
            'emptyText' => 'Новых заданий по заданным критериям не найдено',
        ]);
        ?>

        <?= LinkPager::widget([
            'pagination' => $pagination,
            'options' =>
                ['class' => 'pagination-list'],
            'activePageCssClass' => 'pagination-item--active',
            'pageCssClass' => 'pagination-item',
            'prevPageCssClass' => 'pagination-item mark',
            'prevPageLabel' => $pagination->getPage() === 0 ? false : '',
            'nextPageCssClass' => 'pagination-item mark',
            'nextPageLabel' => $pagination->getPage() === $pagination->getPageCount() - 1 ? false : '',
            'linkOptions' => ['class' => 'link link--page'],
        ]);

        ?>
    </div>
    <div class="right-column">
        <div class="right-card black">
            <div class="search-form">
                <?php
                $form = ActiveForm::begin([
                    'method' => 'get',
                    'action' => ['/tasks'],
                ]) ?>

                <h4 class="head-card"><?= Html::encode($model->getAttributeLabel('categories')) ?></h4>
                <div class="form-group">
                    <?= $form->field($model, 'categories', [
                        'template' => '<div class="checkbox-wrapper">{input}</div>',
                    ])->checkboxList($categories, [
                        'item' => function ($index, $label, $name, $checked, $value) {
                            $id = 'category-' . $value;
                            $checkbox = Html::checkbox($name, $checked, ['value' => $value, 'id' => $id]);
                            return Html::label($checkbox . ' ' . Html::encode($label), $id, ['class' => 'control-label']
                            );
                        },
                        'tag' => false,
                    ])->label(false);
                    ?>
                </div>

                <h4 class="head-card">Дополнительно</h4>
                <div class="form-group">
                    <?= $form->field(
                        $model,
                        'checkWorker',
                        [
                            'options' => [
                                'class' => 'checkbox-wrapper'
                            ],
                            'template' => '<div class="control-label">{input}</div>',
                        ]
                    )->checkbox()
                    ?>
                </div>

                <h4 class="head-card"><?= Html::encode($model->getAttributeLabel('createdAt')) ?></h4>
                <div class="form-group">
                    <?= $form->field($model, 'createdAt', [
                        'template' => '{input}',
                    ])->dropDownList(
                        TaskSearch::getPeriodOptions(),
                        [
                            'id' => 'period-value',
                            'prompt' => 'Выберите период'
                        ]
                    )->label(false)
                    ?>
                </div>

                <?= Html::submitInput('Искать', ['class' => 'button button--blue']) ?>

                <?php
                ActiveForm::end() ?>
            </div>
        </div>
    </div>
</main>
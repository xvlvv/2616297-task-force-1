<?php

declare(strict_types = 1);

use app\assets\AutocompleteAsset;
use app\models\PublishForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var PublishForm $formModel Модель формы публикации задания
 * @var array $categories Список категорий [идентификатор => название]
 */

AutocompleteAsset::register($this);

?>
<div class="add-task-form regular-form center-block">
    <?php
    $form = ActiveForm::begin([
        'method' => 'post',
        'action' => ['/publish'],
    ]) ?>

    <h3 class="head-main head-main">Публикация нового задания</h3>
    <?= $form->field($formModel, 'name')->textInput() ?>

    <?= $form->field($formModel, 'description')->textarea() ?>

    <?= $form->field($formModel, 'categoryId')->dropDownList($categories) ?>

    <?= $form->field($formModel, 'location')->textInput(
        ['class' => 'location-input location-icon', 'data-autocomplete' => 'location']
    ) ?>

    <?= Html::activeHiddenInput($formModel, 'latitude', ['data-publish-hidden' => 'latitude']) ?>

    <?= Html::activeHiddenInput($formModel, 'longitude', ['data-publish-hidden' => 'longitude']) ?>

    <?= Html::activeHiddenInput($formModel, 'additionalInfo', ['data-publish-hidden' => 'additionalInfo']) ?>

    <div class="half-wrapper">
        <?= $form->field($formModel, 'budget')->textInput(['class' => 'budget-icon']) ?>

        <?= $form->field($formModel, 'endDate')->input('date') ?>
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

<?php
$searchUrl = Url::to(['api/locations']);
$modelId = $formModel->formName();

$js = <<<JS

const autoCompleteJS = new autoComplete({
    placeHolder: 'Введите город',
    selector: '[data-autocomplete="location"]',
    data: {
        src: async (query) => {
            try {
                const source = await fetch(`{$searchUrl}?query=\${query}`);
                return await source.json();
            } catch (error) {
                return error;
            }
        },
        keys: ['fullAddress'],
        cache: false
    },
    threshold: 2,
    debounce: 500,
    searchEngine: 'loose',
    resultsList: {
        noResults: true,
        maxResults: 10,
        element: (list, data) => {
            if (!data.results.length) {
                const message = document.createElement('li');
                message.setAttribute('class', 'no-results-message');
                message.textContent = 'Ничего не найдено';
                list.appendChild(message);
            }
        }
    },
    resultItem: {
        highlight: true
    },
    events: {
        input: {
            selection: (event) => {
                const selection = event.detail.selection.value;
                autoCompleteJS.input.value = selection.fullAddress;
                document.querySelector('[data-publish-hidden="latitude"]').value = selection.latitude;
                document.querySelector('[data-publish-hidden="longitude"]').value = selection.longitude;
                document.querySelector('[data-publish-hidden="additionalInfo"]').value = selection.additional || '';
            }
        }
    }
});

JS;

$this->registerJs($js);
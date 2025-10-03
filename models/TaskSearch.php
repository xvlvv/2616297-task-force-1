<?php

declare(strict_types=1);

namespace app\models;

/**
 * Модель формы для фильтрации списка заданий.
 * Наследуется от Task для удобства, но является моделью формы.
 */
class TaskSearch extends Task
{
    /** @var string|array Массив ID выбранных категорий */
    public string|array $categories = [];

    /** @var bool Флаг "Без исполнителя" */
    public bool $checkWorker = false;

    /** @var string Выбранный период времени для фильтрации */
    public string $createdAt = '';

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['categories', 'checkWorker', 'createdAt'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'categories' => 'Категории',
            'checkWorker' => 'Без исполнителя',
            'createdAt' => 'Период'
        ];
    }

    /**
     * Возвращает массив опций для выпадающего списка периодов.
     * @return string[]
     */
    public static function getPeriodOptions(): array
    {
        return [
            1 => '1 час',
            12 => '12 часов',
            24 => '24 часа'
        ];
    }
}
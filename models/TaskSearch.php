<?php

declare(strict_types=1);

namespace app\models;

class TaskSearch extends Task
{
    public string|array $categories = [];
    public bool $checkWorker = false;
    public string $createdAt = '';

    public function rules(): array
    {
        return [
            [['categories', 'checkWorker', 'createdAt'], 'safe'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'categories' => 'Категории',
            'checkWorker' => 'Без исполнителя',
            'createdAt' => 'Период'
        ];
    }

    public static function getPeriodOptions(): array
    {
        return [
            1 => '1 час',
            12 => '12 часов',
            24 => '24 часа'
        ];
    }
}
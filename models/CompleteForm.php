<?php

namespace app\models;

use yii\base\Model;

/**
 * Модель формы для завершения задания.
 * Содержит правила валидации для полей комментария и оценки.
 */
class CompleteForm extends Model
{
    /** @var string Текст отзыва о работе исполнителя */
    public string $comment = '';
    /** @var string|null Оценка работы исполнителя (от 1 до 5) */
    public ?string $rating = null;

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['comment'], 'string'],
            ['rating', 'required', 'message' => 'Пожалуйста, поставьте оценку.'],
            ['rating', 'integer'],
            ['rating', 'in', 'range' => [1, 2, 3, 4, 5]],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels(): array
    {
        return [
            'comment' => 'Ваш комментарий',
            'rating' => 'Оценка работы',
        ];
    }
}
<?php

namespace app\models;

use yii\base\Model;

class CompleteForm extends Model
{
    public string $comment = '';
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
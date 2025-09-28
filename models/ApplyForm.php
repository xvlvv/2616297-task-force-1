<?php

namespace app\models;

use yii\base\Model;

class ApplyForm extends Model
{
    public ?string $description = null;
    public ?string $price = null;

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['description'], 'trim'],
            [['description'], 'safe'],
            [['description'], 'string', 'max' => 500, 'message' => 'Превышен лимит символов комментария'],
            ['price', 'integer', 'min' => 0, 'message' => 'Стоимость задания должна быть больше 0'],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels(): array
    {
        return [
            'description' => 'Ваш комментарий',
            'price' => 'Стоимость',
        ];
    }
}
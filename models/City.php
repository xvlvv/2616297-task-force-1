<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * ActiveRecord модель для таблицы "{{%city}}".
 * Представляет город.
 *
 * @property int $id
 * @property string $name Название города
 * @property float $latitude Широта
 * @property float $longitude Долгота
 * @property string|null $created_at Время создания
 * @property string|null $updated_at Время последнего обновления
 *
 * @property Task[] $tasks Задания в этом городе
 * @property User[] $users Пользователи из этого города
 */
class City extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%city}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'latitude', 'longitude'], 'required'],
            [['latitude'], 'number', 'min' => -90, 'max' => 90],
            [['longitude'], 'number', 'min' => -180, 'max' => 180],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'latitude' => 'Широта',
            'longitude' => 'Долгота',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлён',
        ];
    }

    /**
     * Определяет связь с заданиями (Task)
     *
     * @return ActiveQuery
     */
    public function getTasks(): ActiveQuery
    {
        return $this->hasMany(Task::class, ['city_id' => 'id']);
    }


    /**
     * Определяет связь с пользователями (User)
     *
     * @return ActiveQuery
     */
    public function getUsers(): ActiveQuery
    {
        return $this->hasMany(User::class, ['city_id' => 'id']);
    }

}

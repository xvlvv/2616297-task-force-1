<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * ActiveRecord модель для таблицы "{{%category}}".
 * Представляет категорию заданий.
 *
 * @property int $id
 * @property string $name Название категории
 * @property string|null $icon CSS-класс иконки
 * @property string|null $created_at Время создания
 * @property string|null $updated_at Время последнего обновления
 *
 * @property Task[] $tasks Задания, принадлежащие этой категории
 * @property UserSpecialization[] $userSpecializations Связи пользователей с этой категорией
 * @property User[] $users Пользователи, выбравшие эту категорию как специализацию
 */
class Category extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%category}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['icon'], 'default', 'value' => null],
            [['name'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['icon'], 'string', 'max' => 255],
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
            'name' => 'Название',
            'icon' => 'Иконка',
            'created_at' => 'Создана',
            'updated_at' => 'Обновлена',
        ];
    }

    /**
     * Определяет связь с заданиями (Task)
     *
     * @return ActiveQuery
     */
    public function getTasks(): ActiveQuery
    {
        return $this->hasMany(Task::class, ['category_id' => 'id']);
    }

    /**
     * Определяет связь со специализациями пользователей (UserSpecialization)
     *
     * @return ActiveQuery
     */
    public function getUserSpecializations(): ActiveQuery
    {
        return $this->hasMany(UserSpecialization::class, ['category_id' => 'id']);
    }
}

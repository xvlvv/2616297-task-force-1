<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * ActiveRecord модель для таблицы "{{%file}}".
 * Представляет файл, прикрепленный к заданию.
 *
 * @property int $id
 * @property string $original_name Оригинальное имя файла
 * @property int $task_id ID связанного задания
 * @property string $path Путь к файлу на сервере
 * @property string $mime_type MIME-тип файла
 * @property string|null $created_at Время создания
 * @property string|null $updated_at Время последнего обновления
 *
 * @property Task $task Связанная модель задания
 */
class File extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%file}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['original_name', 'path', 'mime_type'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['original_name', 'path'], 'string', 'max' => 255],
            [['mime_type'], 'string', 'max' => 128],
            [['mime_type'], 'in', 'range' => ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']],
            [['path'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'original_name' => 'Оригинальное имя',
            'path' => 'Путь',
            'mime_type' => 'Mime тип',
            'size_bytes' => 'Размер',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлён',
        ];
    }

    /**
     * Определяет связь с моделью Task
     *
     * @return ActiveQuery
     */
    public function getTask(): ActiveQuery
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }

}

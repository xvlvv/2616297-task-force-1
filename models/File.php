<?php

namespace app\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%file}}".
 *
 * @property int $id
 * @property string $original_name
 * @property string $path
 * @property string $mime_type
 * @property int $size_bytes
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property TaskFile[] $taskFiles
 * @property Task[] $tasks
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
            [['original_name', 'path', 'mime_type', 'size_bytes'], 'required'],
            [['size_bytes'], 'integer', 'min' => 1],
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
     * Gets query for [[TaskFiles]].
     *
     * @return ActiveQuery
     */
    public function getTaskFiles(): ActiveQuery
    {
        return $this->hasMany(TaskFile::class, ['file_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getTasks(): ActiveQuery
    {
        return $this->hasMany(Task::class, ['id' => 'task_id'])->viaTable('{{%task_file}}', ['file_id' => 'id']);
    }

}

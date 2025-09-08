<?php

namespace app\models;

use Yii;

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
class File extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%file}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['original_name', 'path', 'mime_type', 'size_bytes'], 'required'],
            [['size_bytes'], 'integer'],
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
    public function attributeLabels()
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
     * @return \yii\db\ActiveQuery
     */
    public function getTaskFiles()
    {
        return $this->hasMany(TaskFile::class, ['file_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::class, ['id' => 'task_id'])->viaTable('{{%task_file}}', ['file_id' => 'id']);
    }

}

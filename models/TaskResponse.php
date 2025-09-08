<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%task_response}}".
 *
 * @property int $id
 * @property int $task_id
 * @property int $worker_id
 * @property string|null $comment
 * @property int|null $price
 * @property int $is_rejected
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Task $task
 * @property User $worker
 */
class TaskResponse extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%task_response}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['comment', 'price'], 'default', 'value' => null],
            [['task_id', 'worker_id'], 'required'],
            [['task_id', 'worker_id', 'price'], 'integer', 'min' => 1],
            [['is_rejected'], 'boolean'],
            [['comment'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],
            [['worker_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['worker_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'ID задания',
            'worker_id' => 'ID исполнителя',
            'comment' => 'Комментарий',
            'price' => 'Цена',
            'is_rejected' => 'Отклонён',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлён',
        ];
    }

    /**
     * Gets query for [[Task]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }

    /**
     * Gets query for [[Worker]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWorker()
    {
        return $this->hasOne(User::class, ['id' => 'worker_id']);
    }

}

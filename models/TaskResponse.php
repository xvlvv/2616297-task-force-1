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
            [['is_rejected'], 'default', 'value' => 0],
            [['task_id', 'worker_id'], 'required'],
            [['task_id', 'worker_id', 'price', 'is_rejected'], 'integer'],
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
            'task_id' => 'Task ID',
            'worker_id' => 'Worker ID',
            'comment' => 'Comment',
            'price' => 'Price',
            'is_rejected' => 'Is Rejected',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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

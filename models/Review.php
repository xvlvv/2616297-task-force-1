<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%review}}".
 *
 * @property int $id
 * @property int $task_id
 * @property int $customer_id
 * @property int $worker_id
 * @property string $comment
 * @property int $rating
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property User $customer
 * @property Task $task
 * @property User $worker
 */
class Review extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%review}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['task_id', 'customer_id', 'worker_id', 'comment', 'rating'], 'required'],
            [['task_id', 'customer_id', 'worker_id', 'rating'], 'integer', 'min' => 1],
            [['is_rejected'], 'boolean'],
            [['comment'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['task_id'], 'unique'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['customer_id' => 'id']],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],
            [['worker_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['worker_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'task_id' => 'ID задания',
            'customer_id' => 'ID клиента',
            'worker_id' => 'ID исполнителя',
            'comment' => 'Комментарий',
            'rating' => 'Рейтинг',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлён',
        ];
    }

    /**
     * Gets query for [[Customer]].
     *
     * @return ActiveQuery
     */
    public function getCustomer(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'customer_id']);
    }

    /**
     * Gets query for [[Task]].
     *
     * @return ActiveQuery
     */
    public function getTask(): ActiveQuery
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }

    /**
     * Gets query for [[Worker]].
     *
     * @return ActiveQuery
     */
    public function getWorker(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'worker_id']);
    }
}

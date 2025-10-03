<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * ActiveRecord модель для таблицы "{{%task_response}}".
 * Представляет отклик исполнителя на задание.
 *
 * @property int $id
 * @property int $task_id ID связанного задания
 * @property int $worker_id ID исполнителя, оставившего отклик
 * @property string|null $comment Комментарий к отклику
 * @property int|null $price Предложенная стоимость
 * @property int $is_rejected Флаг, отклонен ли отклик заказчиком
 * @property string|null $created_at Время создания
 * @property string|null $updated_at Время последнего обновления
 *
 * @property Task $task Связанная модель задания
 * @property User $worker Связанная модель пользователя-исполнителя
 */
class TaskResponse extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%task_response}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['comment', 'price'], 'default', 'value' => null],
            [['task_id', 'worker_id'], 'required'],
            [['task_id', 'worker_id'], 'integer', 'min' => 1],
            [['is_rejected'], 'boolean'],
            [['comment'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [
                ['task_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Task::class,
                'targetAttribute' => ['task_id' => 'id']
            ],
            [
                ['worker_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['worker_id' => 'id']
            ],
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
            'worker_id' => 'ID исполнителя',
            'comment' => 'Комментарий',
            'price' => 'Цена',
            'is_rejected' => 'Отклонён',
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

    /**
     * Определяет связь с моделью User (исполнитель)
     *
     * @return ActiveQuery
     */
    public function getWorker(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'worker_id']);
    }

}

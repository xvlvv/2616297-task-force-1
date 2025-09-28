<?php

namespace Xvlvv\DataMapper;

use app\models\Category;
use app\models\Task;
use Xvlvv\Entity\Category as CategoryEntity;
use Xvlvv\Entity\City;
use Xvlvv\Entity\Task as TaskEntity;
use Xvlvv\Enums\Status;
use yii\web\NotFoundHttpException;

class TaskMapper
{
    public function toDomainEntity(Task $task): TaskEntity
    {
        $city = new City(
            $task->city->id,
            $task->city->name
        );

        $status = Status::from($task->status);
        $category = Category::find()->where(['id' => $task->category->id])->one();

        $categoryEntity = new CategoryEntity(
            $task->category->id,
            $task->category->name
        );

        if (null === $category) {
            throw new NotFoundHttpException('Отсутствует категория у задачи');
        }

        return \Xvlvv\Entity\Task::create(
            $task->customer_id,
            $task->name,
            $task->description,
            $task->created_at,
            $categoryEntity,
            $task->end_date,
            $task->worker_id,
            $task->id,
            $task->budget,
            $status,
            $city,
        );
    }
}
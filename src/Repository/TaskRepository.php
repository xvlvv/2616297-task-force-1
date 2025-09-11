<?php

declare(strict_types = 1);

namespace Xvlvv\Repository;

use Xvlvv\DTO\RenderTaskDTO;
use Xvlvv\DTO\SaveTaskDTO;
use Xvlvv\Entity\City;
use Xvlvv\Entity\Task;
use \app\models\Task as TaskModel;
use Xvlvv\Enums\Status;
use yii\web\NotFoundHttpException;

class TaskRepository implements TaskRepositoryInterface
{
    /* Пока что не сделал реализацию оставшихся методов,
    как дойду по заданию то сделаю, пока что для отклика например
    ещё нет фикстур */
    public function save(SaveTaskDTO $task): ?int
    {
        // TODO: Implement save() method.
    }

    public function update(Task $task): Task
    {
        // TODO: Implement update() method.
    }

    public function hasAlreadyResponded(int $taskId, int $userId): bool
    {
        // TODO: Implement hasAlreadyResponded() method.
    }

    public function isAuthor(int $taskId, int $userId): bool
    {
        $task = $this->getModelByIdOrFail($taskId);
        $customerId = $task->customer_id;

        return $userId === $customerId;
    }

    public function isWorker(int $taskId, int $userId): bool
    {
        $task = $this->getModelByIdOrFail($taskId);
        $workerId = $task->worker_id;

        return $userId === $workerId;
    }
    public function getWorkerByIdOrFail(int $taskId): int
    {
        $task = $this->getModelByIdOrFail($taskId);
        $workerId = $task->worker_id;
        if (null === $workerId) {
            throw new NotFoundHttpException('Task has no worker with id ' . $taskId);
        }
        return $workerId;
    }

    public function getByIdOrFail(int $taskId): Task
    {
        $task = $this->getModelByIdOrFail($taskId);

        $city = new City(
            $task->city->id,
            $task->city->name
        );

        $status = Status::from($task->status);

        return Task::create(
            $task->customer_id,
            $task->worker_id,
            $task->id,
            $status,
            $city,
        );
    }

    public function getNewTasks(int $offset, int $limit): array
    {
        $tasksModels = TaskModel::find()
            ->with('category', 'city')
            ->where(['status' => Status::NEW])
            ->offset($offset)
            ->limit($limit)
            ->all();
        return array_map(function ($task) {
            return new RenderTaskDTO(
                $task->id,
                $task->name,
                $task->description,
                $task->category->name,
                $task->city->name,
                $task->budget,
                $task->created_at,
            );
        }, $tasksModels);
    }

    private function getModelByIdOrFail(int $taskId): TaskModel
    {
        $task = TaskModel::findOne($taskId);
        if ($task === null) {
            throw new NotFoundHttpException();
        }
        return $task;
    }
}
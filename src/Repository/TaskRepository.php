<?php

declare(strict_types = 1);

namespace Xvlvv\Repository;

use app\models\TaskResponse;
use DateTime;
use http\Exception\InvalidArgumentException;
use http\Exception\RuntimeException;
use Xvlvv\DTO\GetNewTasksDTO;
use Xvlvv\DTO\RenderTaskDTO;
use Xvlvv\DTO\SaveTaskDTO;
use Xvlvv\DTO\ViewNewTasksDTO;
use Xvlvv\Entity\City;
use Xvlvv\Entity\Task;
use \app\models\Task as TaskModel;
use Xvlvv\Enums\Status;
use yii\db\ActiveQuery;
use yii\db\Exception;
use yii\db\Query;
use yii\web\NotFoundHttpException;

class TaskRepository implements TaskRepositoryInterface
{
    public function save(SaveTaskDTO $taskDTO): ?int
    {
        $task = new TaskModel();
        $task->name = $taskDTO->name;
        $task->status = Status::NEW;
        $task->description = $taskDTO->description;
        $task->category_id = $taskDTO->category->getId();
        $task->customer_id = $taskDTO->customer->getId();
        $task->end_date = $taskDTO->endDate;
        $task->budget = $taskDTO->budget;
        $task->city_id = $taskDTO->city->getId();
        $task->longitude = $taskDTO->coordinates->longitude;
        $task->latitude = $taskDTO->coordinates->latitude;
        try {
            $task->save();
        } catch (
            Exception $exception
        ) {
            return null;
        }
        return $task->id;
    }

    public function update(Task $task): void
    {
        $taskId = $task->getId();
        if (null === $taskId) {
            throw new InvalidArgumentException('Cannot update unsaved task');
        }

        $taskModel = TaskModel::findOne($taskId);

        if (null === $taskModel) {
            throw new InvalidArgumentException('Cannot update unsaved task');
        }

        if ($taskModel->status !== $task->getCurrentStatus()->value) {
            $taskModel->status = $task->getCurrentStatus();
        }

        if ($taskModel->worker_id !== $task->getWorkerId()) {
            $taskModel->worker_id = $task->getWorkerId();
        }

        try {
            $taskModel->save();
        } catch (
            Exception $exception
        ) {
            throw new RuntimeException('Failed to update task');
        }
    }

    public function hasAlreadyResponded(int $taskId, int $userId): bool
    {
        $taskResponse = TaskResponse::find()
            ->where(['task_id' => $taskId, 'worker_id' => $userId])
            ->one();

        return null !== $taskResponse;
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
            $task->budget,
            $status,
            $city,
        );
    }

    private function getNewTasksQuery(): ActiveQuery
    {
        return TaskModel::find()
            ->where(['status' => Status::NEW]);
    }

    private function getFilteredTasksQuery(GetNewTasksDTO $dto): ActiveQuery
    {
        $query = $this->getNewTasksQuery();

        $query
            ->with('category', 'city')
            ->offset($dto->offset)
            ->limit($dto->limit)
            ->andFilterWhere(['category_id' => $dto->categories]);

        if (true === $dto->checkWorker) {
            $query->andWhere(['not', ['worker_id' => null]]);
        }

        if (!empty($dto->createdAt)) {
            $pastDate = (new DateTime())
                ->modify("-{$dto->createdAt} hours")
                ->format('Y-m-d H:i:s');

            $query->andWhere(['>=', 'created_at', $pastDate]);
        }

        return $query;
    }

    public function getNewTasks(GetNewTasksDTO $dto): ViewNewTasksDTO
    {
        $categoryQuery = $this->getNewTasksQuery();

        $categories = $categoryQuery
            ->joinWith('category', false)
            ->select(['{{%category}}.id', '{{%category}}.name'])
            ->distinct()
            ->asArray()
            ->all();

        $tasksModels = $this->getFilteredTasksQuery($dto)->all();

        $tasks = array_map(function ($task) {
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

        return new ViewNewTasksDTO(
            $tasks,
            $categories
        );
    }

    private function getModelByIdOrFail(int $taskId): TaskModel
    {
        $task = TaskModel::findOne($taskId);
        if ($task === null) {
            throw new NotFoundHttpException();
        }
        return $task;
    }

    public function getFilteredTasksQueryCount(GetNewTasksDTO $dto): int
    {
        return $this
            ->getFilteredTasksQuery($dto)
            ->count();
    }
}
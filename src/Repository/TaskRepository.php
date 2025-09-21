<?php

declare(strict_types = 1);

namespace Xvlvv\Repository;

use app\models\TaskResponse;
use DateMalformedStringException;
use DateTime;
use http\Exception\InvalidArgumentException;
use http\Exception\RuntimeException;
use Throwable;
use Xvlvv\DTO\GetNewTasksDTO;
use Xvlvv\DTO\RenderTaskDTO;
use Xvlvv\DTO\SaveTaskDTO;
use Xvlvv\DTO\ViewNewTasksDTO;
use Xvlvv\DTO\ViewTaskDTO;
use Xvlvv\Entity\City;
use Xvlvv\Entity\Task;
use app\models\Task as TaskModel;
use Xvlvv\Enums\Status;
use yii\db\ActiveQuery;
use yii\db\Exception;
use yii\web\NotFoundHttpException;

/**
 * Репозиторий для работы с сущностями Task
 */
class TaskRepository implements TaskRepositoryInterface
{
    /**
     * @param TaskResponseRepositoryInterface $taskResponseRepo
     */
    public function __construct(
        private TaskResponseRepositoryInterface $taskResponseRepo,
    ) {
    }

    /**
     * {@inheritdoc}
     */
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
            Throwable
        ) {
            return null;
        }
        return $task->id;
    }

    /**
     * {@inheritdoc}
     */
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
            Exception
        ) {
            throw new RuntimeException('Failed to update task');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasAlreadyResponded(int $taskId, int $userId): bool
    {
        $taskResponse = TaskResponse::find()
            ->where(['task_id' => $taskId, 'worker_id' => $userId])
            ->one();

        return null !== $taskResponse;
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthor(int $taskId, int $userId): bool
    {
        $task = $this->getModelByIdOrFail($taskId);
        $customerId = $task->customer_id;

        return $userId === $customerId;
    }

    /**
     * {@inheritdoc}
     */
    public function isWorker(int $taskId, int $userId): bool
    {
        $task = $this->getModelByIdOrFail($taskId);
        $workerId = $task->worker_id;

        return $userId === $workerId;
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkerByIdOrFail(int $taskId): int
    {
        $task = $this->getModelByIdOrFail($taskId);
        $workerId = $task->worker_id;
        if (null === $workerId) {
            throw new NotFoundHttpException('Task has no worker with id ' . $taskId);
        }
        return $workerId;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * Возвращает ActiveQuery списка заданий со статусом NEW
     *
     * @return ActiveQuery
     */
    private function getNewTasksQuery(): ActiveQuery
    {
        return TaskModel::find()
            ->where(['status' => Status::NEW]);
    }

    /**
     * Возвращает ActiveQuery списка заданий с учётом фильтра
     *
     * @param GetNewTasksDTO $dto Фильтр для выборки задач
     *
     * @return ActiveQuery
     * @throws DateMalformedStringException
     */
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * Находит задание по ID или выбрасывает исключение
     * @param int $taskId
     * @return TaskModel
     * @throws NotFoundHttpException
     */
    private function getModelByIdOrFail(int $taskId): TaskModel
    {
        $task = TaskModel::findOne($taskId);
        if ($task === null) {
            throw new NotFoundHttpException();
        }
        return $task;
    }

    /**
     * {@inheritdoc}
     * @throws DateMalformedStringException
     */
    public function getFilteredTasksQueryCount(GetNewTasksDTO $dto): int
    {
        return $this
            ->getFilteredTasksQuery($dto)
            ->count();
    }

    /**
     * {@inheritdoc}
     */
    public function getTaskForView(int $id): ViewTaskDTO
    {
        $task = $this->getModelByIdOrFail($id);
        $responses = $this->taskResponseRepo->findByTaskId($task->id);
        $status = Status::from($task->status) === Status::NEW ? 'Открыт для новых заказов' : 'Занят';

        return new ViewTaskDTO(
            $task->name,
            $task->budget,
            $task->description,
            $task->category->name,
            $task->created_at,
            $task->end_date,
            $status,
            $responses
        );
    }

    public function workerHasActiveTask(int $id): bool
    {
        $task = TaskModel::find()->where(['worker_id' => $id, 'status' => Status::IN_PROGRESS])->one();

        return null === $task;
    }

    public function getCompletedTasksCountByWorkerId(int $workerId): int
    {
        return (int) TaskModel::find()
            ->where(['worker_id' => $workerId, 'status' => Status::COMPLETED])
            ->count();
    }
}
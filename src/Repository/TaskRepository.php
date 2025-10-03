<?php

declare(strict_types = 1);

namespace Xvlvv\Repository;

use app\models\File;
use app\models\TaskResponse;
use DateMalformedStringException;
use DateTime;
use http\Exception\InvalidArgumentException;
use http\Exception\RuntimeException;
use Throwable;
use Xvlvv\DataMapper\TaskMapper;
use Xvlvv\DTO\GetNewTasksDTO;
use Xvlvv\DTO\RenderTaskDTO;
use Xvlvv\DTO\SaveTaskDTO;
use Xvlvv\DTO\ViewNewTasksDTO;
use Xvlvv\DTO\ViewTaskDTO;
use Xvlvv\Entity\Task;
use app\models\Task as TaskModel;
use Xvlvv\Enums\Status;
use Yii;
use yii\db\ActiveQuery;
use yii\web\NotFoundHttpException;

/**
 * Репозиторий для работы с сущностями Task
 */
readonly final class TaskRepository implements TaskRepositoryInterface
{
    /**
     * @param TaskResponseRepositoryInterface $taskResponseRepo
     * @param TaskMapper $mapper
     */
    public function __construct(
        private TaskResponseRepositoryInterface $taskResponseRepo,
        private TaskMapper $mapper,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function save(SaveTaskDTO $taskDTO): ?int
    {
        $task = new TaskModel();
        $task->name = $taskDTO->name;
        $task->status = Status::NEW->value;
        $task->description = $taskDTO->description;
        $task->category_id = $taskDTO->category->getId();
        $task->customer_id = $taskDTO->customer->getId();
        $task->end_date = $taskDTO->endDate->format('Y-m-d');
        $task->budget = $taskDTO->budget;
        $task->city_id = $taskDTO->city->getId();
        $task->longitude = $taskDTO->coordinates->longitude ?? null;
        $task->latitude = $taskDTO->coordinates->latitude ?? null;
        $task->location_info = $taskDTO->locationAdditionalInfo ?? null;

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if (!$task->save()) {
                throw new \Exception();
            }

            $taskId = $task->id;

            foreach ($taskDTO->files as $file) {
                $fileModel = new File();
                $fileModel->task_id = $taskId;
                $fileModel->original_name = $file->originalName;
                $fileModel->path = $file->filePath;
                $fileModel->mime_type = mime_content_type($file->filePath);

                if (!$fileModel->save()) {
                    throw new \Exception();
                }
            }

            $transaction->commit();
            return $taskId;
        } catch (
        Throwable
        ) {
            $transaction->rollBack();
            return null;
        }


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
            $taskModel->status = $task->getCurrentStatus()->value;
        }

        if ($taskModel->worker_id !== $task->getWorkerId()) {
            $taskModel->worker_id = $task->getWorkerId();
        }

        if (!$taskModel->save()) {
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

        return $this->mapper->toDomainEntity($task);
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

        $tasksModels = $this
            ->getFilteredTasksQuery($dto)
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        $tasks = array_map(function (TaskModel $task) {
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
        $task = TaskModel::find()->with('category', 'city')->where(['id' => $taskId])->one();
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
    public function getTaskForView(int $id, int $userId): ViewTaskDTO
    {
        $task = $this->getByIdOrFail($id);
        $responses = $this->taskResponseRepo->findByTaskId($task->getId(), $userId);
        $files = File::find()->where(['task_id' => $task->getId()])->all();

        return new ViewTaskDTO(
            $task->getId(),
            $task->getName(),
            $task->getBudget(),
            $task->getDescription(),
            $task->getCategory()->getName(),
            $task->getCreatedDate(),
            $task->getEndDate(),
            $task->getCurrentStatus(),
            $task->getAvailableActions($userId),
            $responses,
            $files,
            $task->getCityName(),
            $task->getCoordinates(),
            $task->getLocationInfo(),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function workerHasActiveTask(int $id): bool
    {
        $task = TaskModel::find()->where(['worker_id' => $id, 'status' => Status::IN_PROGRESS])->one();

        return null === $task;
    }

    /**
     * {@inheritDoc}
     */
    public function getCompletedTasksCountByWorkerId(int $workerId): int
    {
        return (int) TaskModel::find()
            ->where(['worker_id' => $workerId, 'status' => Status::COMPLETED])
            ->count();
    }

    /**
     * {@inheritDoc}
     */
    public function findForUserByStatuses(int $userId, array $statuses): array
    {
        $query = TaskModel::find()
            ->alias('task')
            ->distinct()
            ->with([
                'category',
            ])
            ->leftJoin(TaskResponse::tableName() . ' response', 'response.task_id = task.id')
            ->where(['task.status' => $statuses])
            ->andWhere(['or',
                ['task.customer_id' => $userId],
                ['response.worker_id' => $userId]
            ])
            ->orderBy(['task.created_at' => SORT_DESC]);

        $arTasks = $query->all();

        return array_map(
            function (TaskModel $task) {
                return $this->mapper->toDomainEntity($task);
            }, $arTasks
        );
    }
}
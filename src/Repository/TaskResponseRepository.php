<?php

declare(strict_types=1);

namespace Xvlvv\Repository;

use app\models\Task;
use app\models\TaskResponse as Model;
use app\models\User;
use http\Exception\RuntimeException;
use LogicException;
use Xvlvv\DataMapper\TaskMapper;
use Xvlvv\DataMapper\UserMapper;
use Xvlvv\DTO\TaskResponseViewDTO;
use Xvlvv\Entity\TaskResponse;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Репозиторий для работы с откликами на задачи
 */
class TaskResponseRepository implements TaskResponseRepositoryInterface
{
    /**
     * @param ReviewRepositoryInterface $reviewRepo
     * @param UserMapper $userMapper
     * @param TaskRepositoryInterface $taskRepo
     */
    public function __construct(
        private ReviewRepositoryInterface $reviewRepo,
        private UserMapper $userMapper,
        private TaskMapper $taskMapper,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function findByTaskId(int $id, int $currentUserId): array
    {
        $isAuthor = Task::find()
            ->where(['id' => $id, 'customer_id' => $currentUserId])
            ->exists();

        $query = Model::find()
            ->where(['task_id' => $id]);

        if (!$isAuthor) {
            $query->andWhere(['worker_id' => $currentUserId]);
        }

        $responses = $query->all();

        if (empty($responses)) {
            return [];
        }

        $workerIds = array_unique(ArrayHelper::getColumn($responses, 'worker_id'));
        $workers = User::findWithRating()
            ->where(['id' => $workerIds])
            ->indexBy('id')
            ->all();

        $result = [];

        foreach ($responses as $response) {
            $worker = $workers[$response['worker_id']] ?? null;

            if (empty($worker)) {
                continue;
            }

            $result[] = new TaskResponseViewDTO(
                $response->id,
                $worker->id,
                $worker->name,
                (float)$worker->rating,
                $worker->reviewsCount,
                $worker->avatar_path,
                $response->created_at,
                $response->price,
                (bool)$response->is_rejected,
                $response->comment,
            );
        }

        return $result;
    }

    public function update(TaskResponse $taskResponse): bool
    {
        $model = Model::find()->where(['id' => $taskResponse->getId()])->one();

        if (null === $model) {
            throw new LogicException('Cannot update unsaved task response');
        }

        $model->is_rejected = $taskResponse->isRejected();

        return $model->save();
    }

    /**
     * {@inheritdoc}
     */
    public function save(TaskResponse $taskResponse): bool
    {
        $taskResponseModel = new Model();
        $taskResponseModel->task_id = $taskResponse->getTaskId();
        $taskResponseModel->worker_id = $taskResponse->getWorkerId();
        $taskResponseModel->comment = $taskResponse->getComment();
        $taskResponseModel->price = $taskResponse->getPrice();
        $taskResponseModel->is_rejected = $taskResponse->isRejected();

        if (!$taskResponseModel->save()) {
            throw new RuntimeException('Ошибка сохранения профиля пользователя');
        }

        return true;
    }

    public function getTaskIdByResponseId(int $id): int
    {
        return $this->getByIdOrFail($id)->getTaskId();
    }

    public function getByIdOrFail(int $id): TaskResponse
    {
        $response = Model::find()
            ->with('worker', 'task')
            ->where(['id' => $id])
            ->one();

        if (null === $response) {
            throw new NotFoundHttpException();
        }

        $task = $this->taskMapper->toDomainEntity($response->task);
        $user = $this->userMapper->toDomainEntity($response->worker);

        return TaskResponse::create(
            $response->id,
            $task,
            $user,
            (bool)$response->is_rejected,
            $response->price,
            $response->comment,
        );
    }
}
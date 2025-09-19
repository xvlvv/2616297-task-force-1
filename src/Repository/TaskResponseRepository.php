<?php

namespace Xvlvv\Repository;

use app\models\User;
use Xvlvv\DTO\TaskResponseViewDTO;
use Xvlvv\Entity\TaskResponse;
use \app\models\TaskResponse as Model;
use yii\helpers\ArrayHelper;

class TaskResponseRepository implements TaskResponseRepositoryInterface
{
    public function __construct(
        private ReviewRepositoryInterface $reviewRepo,
    ) {
    }

    public function save(TaskResponse $taskResponse): bool
    {
        $taskResponseModel = new Model();
        $taskResponseModel->task_id = $taskResponse->getTaskId();
        $taskResponseModel->worker_id = $taskResponse->getWorkerId();
        $taskResponseModel->comment = $taskResponse->getComment();
        $taskResponseModel->price = $taskResponse->getPrice();
        $taskResponseModel->is_rejected = $taskResponse->isRejected();

        $taskResponseModel->save();
        return $taskResponseModel->id;
    }

    public function findByTaskId(int $id): array
    {
        $responses = Model::find()
            ->where(['task_id' => $id])
            ->all();

        if (empty($responses)) {
            return [];
        }

        $workerIds = array_unique(ArrayHelper::getColumn($responses, 'worker_id',));
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
                $worker->id,
                $worker->name,
                (float) $worker->rating,
                $worker->reviewsCount,
                $worker->avatar_path,
                $response->created_at,
                $response->comment,
                $response->price,
            );
        }

        return $result;
    }
}
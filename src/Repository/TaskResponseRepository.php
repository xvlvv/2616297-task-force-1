<?php

namespace Xvlvv\Repository;

use Xvlvv\Entity\TaskResponse;
use \app\models\TaskResponse as Model;

class TaskResponseRepository implements TaskResponseRepositoryInterface
{

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
}
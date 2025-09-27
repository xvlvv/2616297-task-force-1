<?php

namespace app\rbac;

use Xvlvv\Repository\TaskRepositoryInterface;
use Yii;
use yii\rbac\Rule;

class TaskWorkerRule extends Rule
{
    public $name = 'isTaskWorker';

    /**
     * @inheritDoc
     */
    public function execute($user, $item, $params): bool
    {
        $taskRepo = Yii::$container->get(TaskRepositoryInterface::class);
        $taskId = $params['taskId'] ?? null;

        if (null === $taskId) {
            return false;
        }

        return $taskRepo->isWorker($taskId, (int) $user);
    }
}
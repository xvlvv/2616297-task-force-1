<?php

namespace app\rbac;

use Xvlvv\Repository\TaskRepositoryInterface;
use Yii;
use yii\base\InvalidConfigException;
use yii\rbac\Rule;

class TaskAuthorRule extends Rule
{
    public $name = 'isTaskAuthor';
    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    public function execute($user, $item, $params): bool
    {
        $taskRepo = Yii::$container->get(TaskRepositoryInterface::class);
        $taskId = $params['taskId'] ?? null;

        if (null === $taskId) {
            return false;
        }

        return $taskRepo->isAuthor($taskId, (int) $user);
    }
}
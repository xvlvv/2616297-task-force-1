<?php

namespace app\rbac;

use Xvlvv\Repository\TaskRepositoryInterface;
use Yii;
use yii\rbac\Item;
use yii\rbac\Rule;

/**
 * Правило RBAC для проверки, является ли пользователь назначенным исполнителем задания.
 */
class TaskWorkerRule extends Rule
{
    /** @var string Имя правила */
    public $name = 'isTaskWorker';

    /**
     * Выполняет правило.
     *
     * @param int|string $user ID пользователя.
     * @param Item $item Разрешение или роль, к которым применяется правило.
     * @param array $params Параметры, переданные в `Yii::$app->user->can()`.
     *                      Ожидается наличие ключа 'taskId'.
     * @return bool Результат выполнения правила.
     */
    public function execute($user, $item, $params): bool
    {
        $taskRepo = Yii::$container->get(TaskRepositoryInterface::class);
        $taskId = $params['taskId'] ?? null;

        if (null === $taskId) {
            return false;
        }

        return $taskRepo->isWorker($taskId, (int)$user);
    }
}
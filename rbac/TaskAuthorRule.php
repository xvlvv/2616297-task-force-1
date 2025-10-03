<?php

namespace app\rbac;

use Xvlvv\Repository\TaskRepositoryInterface;
use Yii;
use yii\base\InvalidConfigException;
use yii\rbac\Item;
use yii\rbac\Rule;

/**
 * Правило RBAC для проверки, является ли пользователь автором (заказчиком) задания.
 */
class TaskAuthorRule extends Rule
{
    /** @var string Имя правила */
    public $name = 'isTaskAuthor';

    /**
     * Выполняет правило.
     *
     * @param int|string $user ID пользователя
     * @param Item $item Разрешение или роль, к которым применяется правило
     * @param array $params Параметры, переданные в `Yii::$app->user->can()`.
     *                      Ожидается наличие ключа 'taskId'.
     * @return bool Результат выполнения правила
     * @throws InvalidConfigException
     */
    public function execute($user, $item, $params): bool
    {
        $taskRepo = Yii::$container->get(TaskRepositoryInterface::class);
        $taskId = $params['taskId'] ?? null;

        if (null === $taskId) {
            return false;
        }

        return $taskRepo->isAuthor($taskId, (int)$user);
    }
}
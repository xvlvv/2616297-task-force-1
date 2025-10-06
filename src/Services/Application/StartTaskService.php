<?php

declare(strict_types=1);

namespace Xvlvv\Services\Application;

use Xvlvv\DTO\StartTaskDTO;
use Xvlvv\Exception\PermissionDeniedException;
use Xvlvv\Repository\TaskRepositoryInterface;
use yii\web\NotFoundHttpException;

/**
 * Сервис для назначения исполнителя и старта задачи
 */
readonly final class StartTaskService
{
    /**
     * @param TaskRepositoryInterface $taskRepository
     */
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
    ) {
    }

    /**
     * Назначает исполнителя и запускает задачу
     *
     * @param StartTaskDTO $dto
     * @return bool
     * @throws PermissionDeniedException|NotFoundHttpException если пользователь не является автором задачи
     */
    public function handle(StartTaskDTO $dto): bool
    {
        if (!$this->taskRepository->isAuthor($dto->taskId, $dto->customerId)) {
            throw new PermissionDeniedException('You cannot start this task.');
        }

        $task = $this->taskRepository->getByIdOrFail($dto->taskId);
        $task->start($dto->workerId);
        $this->taskRepository->update($task);

        return true;
    }
}
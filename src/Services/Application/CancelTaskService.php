<?php

declare(strict_types = 1);

namespace Xvlvv\Services\Application;

use Xvlvv\DTO\CancelTaskDTO;
use Xvlvv\Exception\PermissionDeniedException;
use Xvlvv\Repository\TaskRepositoryInterface;

/**
 * Сервис для отмены задачи заказчиком
 */
class CancelTaskService
{
    /**
     * @param TaskRepositoryInterface $taskRepository
     */
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
    ) {
    }

    /**
     * Обрабатывает отмену задачи
     *
     * @param CancelTaskDTO $dto
     * @return bool
     * @throws PermissionDeniedException если пользователь не является автором задачи
     */
    public function handle(CancelTaskDTO $dto): bool
    {
        if (!$this->taskRepository->isAuthor($dto->taskId, $dto->userId)) {
            throw new PermissionDeniedException('You cannot cancel this task.');
        }

        $task = $this->taskRepository->getByIdOrFail($dto->taskId);
        $task->cancel();
        $this->taskRepository->update($task);

        return true;
    }
}
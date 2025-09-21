<?php

declare(strict_types = 1);

namespace Xvlvv\Services\Application;

use Xvlvv\DTO\FailTaskDTO;
use Xvlvv\Exception\PermissionDeniedException;
use Xvlvv\Repository\TaskRepositoryInterface;
use Xvlvv\Repository\UserRepositoryInterface;

/**
 * Сервис для провала задачи исполнителем
 */
class FailTaskService
{
    /**
     * @param TaskRepositoryInterface $taskRepository
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * Обрабатывает провал задачи
     *
     * @param FailTaskDTO $dto
     * @return bool
     * @throws PermissionDeniedException если пользователь не является исполнителем
     */
    public function handle(FailTaskDTO $dto): bool
    {
        if (!$this->taskRepository->isWorker($dto->taskId, $dto->userId)) {
            throw new PermissionDeniedException('You cannot reject this task.');
        }

        $task = $this->taskRepository->getByIdOrFail($dto->taskId);
        $task->fail();
        $this->taskRepository->update($task);

        $user = $this->userRepository->getByIdOrFail($dto->userId);
        $user->increaseFailedTasksCount();
        $this->userRepository->update($user);
        return true;
    }
}
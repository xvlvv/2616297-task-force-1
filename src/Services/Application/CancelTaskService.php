<?php

namespace Xvlvv\Services\Application;

use Xvlvv\DTO\CancelTaskDTO;
use Xvlvv\Exception\PermissionDeniedException;
use Xvlvv\Repository\TaskRepositoryInterface;
use Xvlvv\Repository\UserRepositoryInterface;

class CancelTaskService
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
    ) {
    }

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
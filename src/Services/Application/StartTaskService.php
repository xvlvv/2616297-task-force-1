<?php

namespace Xvlvv\Services\Application;

use Xvlvv\DTO\StartTaskDTO;
use Xvlvv\Exception\PermissionDeniedException;
use Xvlvv\Repository\TaskRepositoryInterface;

final class StartTaskService
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
    ) {
    }

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
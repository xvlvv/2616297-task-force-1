<?php

namespace Xvlvv\Services\Application;

use Xvlvv\DTO\SaveTaskResponseDTO;
use Xvlvv\Entity\TaskResponse;
use Xvlvv\Entity\User;
use Xvlvv\Exception\DuplicateTaskResponseException;
use Xvlvv\Exception\UserCannotApplyToTaskException;
use Xvlvv\Repository\TaskRepositoryInterface;
use Xvlvv\Repository\TaskResponseRepositoryInterface;
use Xvlvv\Repository\UserRepositoryInterface;

final class TaskResponseService
{
    public function __construct(
        private readonly TaskResponseRepositoryInterface $repository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly TaskRepositoryInterface $taskRepository,
    ) {
    }

    public function createResponse(SaveTaskResponseDTO $dto): bool
    {
        $user = $this->userRepository->getByIdOrFail($dto->userId);
        if (false === $user->canApplyToTask()) {
            throw new UserCannotApplyToTaskException('You cannot create task response');
        }

        if (true === $this->taskRepository->hasAlreadyResponded($dto->taskId, $user->getId())) {
            throw new DuplicateTaskResponseException('You have already responded to this task');
        }

        $task = $this->taskRepository->getByIdOrFail($dto->taskId);

        $taskResponse = TaskResponse::create(
            $dto->taskId,
            $task,
            $user,
            $dto->isRejected,
            $dto->price,
            $dto->comment
        );

        return $this->repository->save($taskResponse);
    }
}
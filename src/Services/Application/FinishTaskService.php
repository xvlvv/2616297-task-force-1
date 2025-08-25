<?php

namespace Xvlvv\Services\Application;

use Xvlvv\DTO\CreateReviewDTO;
use Xvlvv\DTO\SaveReviewDTO;
use Xvlvv\Exception\UserIsNotAuthorId;
use Xvlvv\Repository\ReviewRepositoryInterface;
use Xvlvv\Repository\TaskRepositoryInterface;

final class FinishTaskService
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private ReviewRepositoryInterface $reviewRepository,
    ) {
    }

    public function handle(SaveReviewDTO $dto): bool
    {
        if (!$this->taskRepository->isAuthor($dto->taskId, $dto->authorId)) {
            throw new UserIsNotAuthorId('You cannot finish this task.');
        }

        $task = $this->taskRepository->getByIdOrFail($dto->taskId);
        $task->finish();
        $this->taskRepository->update($task);
        $workerId = $this->taskRepository->getWorkerByIdOrFail($dto->taskId);
        $createReviewDTO = new CreateReviewDTO(
            $dto->taskId,
            $dto->authorId,
            $workerId,
            $dto->comment,
            $dto->rating,
        );
        return $this->reviewRepository->save($createReviewDTO);
    }
}
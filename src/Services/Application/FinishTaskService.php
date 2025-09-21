<?php

declare(strict_types = 1);

namespace Xvlvv\Services\Application;

use Xvlvv\DTO\CreateReviewDTO;
use Xvlvv\DTO\SaveReviewDTO;
use Xvlvv\Exception\UserIsNotAuthorId;
use Xvlvv\Repository\ReviewRepositoryInterface;
use Xvlvv\Repository\TaskRepositoryInterface;

/**
 * Сервис для завершения задачи и создания отзыва
 */
final class FinishTaskService
{
    /**
     * @param TaskRepositoryInterface $taskRepository
     * @param ReviewRepositoryInterface $reviewRepository
     */
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private ReviewRepositoryInterface $reviewRepository,
    ) {
    }

    /**
     * Завершает задачу и сохраняет отзыв на исполнителя
     *
     * @param SaveReviewDTO $dto DTO с данными отзыва
     * @return bool
     * @throws UserIsNotAuthorId если пользователь не является автором задачи
     */
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
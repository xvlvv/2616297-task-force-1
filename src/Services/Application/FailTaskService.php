<?php

declare(strict_types = 1);

namespace Xvlvv\Services\Application;

use Xvlvv\DTO\FailTaskDTO;
use Xvlvv\Exception\PermissionDeniedException;
use Xvlvv\Repository\TaskRepositoryInterface;

/**
 * Сервис для провала задачи исполнителем
 */
readonly final class FailTaskService
{
    /**
     * @param TaskRepositoryInterface $taskRepository Репозиторий для работы с заданиями
     */
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
    ) {
    }

    /**
     * Обрабатывает провал задачи
     *
     * @param FailTaskDTO $dto
     * @return bool
     * @throws PermissionDeniedException|\yii\web\NotFoundHttpException если пользователь не является исполнителем
     */
    public function handle(FailTaskDTO $dto): bool
    {
        if (!$this->taskRepository->isWorker($dto->taskId, $dto->userId)) {
            throw new PermissionDeniedException('You cannot reject this task.');
        }

        $task = $this->taskRepository->getByIdOrFail($dto->taskId);
        $task->fail();
        $this->taskRepository->update($task);
        return true;
    }
}
<?php

declare(strict_types = 1);

namespace Xvlvv\Services\Application;

use LogicException;
use Xvlvv\DTO\SaveTaskResponseDTO;
use Xvlvv\Entity\TaskResponse;
use Xvlvv\Exception\DuplicateTaskResponseException;
use Xvlvv\Exception\UserCannotApplyToTaskException;
use Xvlvv\Repository\TaskRepositoryInterface;
use Xvlvv\Repository\TaskResponseRepositoryInterface;
use Xvlvv\Repository\UserRepositoryInterface;

/**
 * Сервис для создания откликов на задачи
 */
final class TaskResponseService
{
    /**
     * @param TaskResponseRepositoryInterface $repository Репозиторий откликов
     * @param UserRepositoryInterface $userRepository Репозиторий пользователей
     * @param TaskRepositoryInterface $taskRepository Репозиторий задач
     */
    public function __construct(
        private readonly TaskResponseRepositoryInterface $repository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly TaskRepositoryInterface $taskRepository,
    ) {
    }

    /**
     * Создает и сохраняет отклик пользователя на задачу
     *
     * @param SaveTaskResponseDTO $dto DTO с данными отклика
     * @return bool
     * @throws UserCannotApplyToTaskException если пользователь не может откликаться на задачи
     * @throws DuplicateTaskResponseException если пользователь уже оставлял отклик
     * @throws LogicException если предложенная цена выше бюджета задачи
     */
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
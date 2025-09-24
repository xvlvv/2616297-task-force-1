<?php

declare(strict_types = 1);

namespace Xvlvv\Services\Application;

use Xvlvv\Domain\ValueObject\Coordinates;
use Xvlvv\DTO\CreateTaskDTO;
use Xvlvv\DTO\SaveTaskDTO;
use Xvlvv\Exception\PermissionDeniedException;
use Xvlvv\Repository\CategoryRepositoryInterface;
use Xvlvv\Repository\CityRepositoryInterface;
use Xvlvv\Repository\TaskRepositoryInterface;
use Xvlvv\Repository\UserRepositoryInterface;
use yii\web\NotFoundHttpException;

/**
 * Сервис для публикации новой задачи
 */
final class PublishTaskService
{
    /**
     * @param CityRepositoryInterface $cityRepository
     * @param TaskRepositoryInterface $taskRepository
     * @param CategoryRepositoryInterface $categoryRepository
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        private readonly CityRepositoryInterface $cityRepository,
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * Публикует новую задачу
     *
     * @param CreateTaskDTO $task DTO с данными для создания задачи
     * @return int|null ID созданной задачи или null в случае ошибки
     * @throws PermissionDeniedException если у пользователя нет прав на создание задач
     * @throws NotFoundHttpException если категория или пользователь не найдены
     */
    public function publish(CreateTaskDTO $task): ?int
    {
        $city = $this->cityRepository->getById(1);
        $category = $this->categoryRepository->getByIdOrFail($task->categoryId);
        $user = $this->userRepository->getByIdOrFail($task->customerId);

        if (false === $user->canCreateTask()) {
            throw new PermissionDeniedException('You do not have permission to create tasks.');
        }

        if (
            null !== $task->latitude
            && null !== $task->longitude
        ) {
            $coordinates = new Coordinates($task->latitude, $task->longitude);
        }

        return $this->taskRepository->save(
            new SaveTaskDTO(
                $task->name,
                $task->description,
                $category,
                $user,
                $task->endDate,
                $coordinates ?? null,
                $task->budget,
                $city,
                $task->files
            )
        );
    }
}
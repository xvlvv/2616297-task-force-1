<?php

namespace Xvlvv\Services\Application;

use Xvlvv\Domain\ValueObject\Coordinates;
use Xvlvv\DTO\CreateTaskDTO;
use Xvlvv\DTO\SaveTaskDTO;
use Xvlvv\Exception\PermissionDeniedException;
use Xvlvv\Repository\CategoryRepositoryInterface;
use Xvlvv\Repository\CityRepositoryInterface;
use Xvlvv\Repository\TaskRepositoryInterface;
use Xvlvv\Repository\UserRepositoryInterface;

final class PublishTaskService
{
    public function __construct(
        private readonly CityRepositoryInterface $cityRepository,
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function publish(CreateTaskDTO $task): ?int
    {
        $city = $this->cityRepository->getById($task->cityId);
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
                $task->fileIds
            )
        );
    }
}
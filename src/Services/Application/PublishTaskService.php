<?php

namespace Xvlvv\Services\Application;

use Xvlvv\DTO\CreateTaskDTO;
use Xvlvv\Entity\Task;
use Xvlvv\Repository\CityRepository;
use Xvlvv\Repository\TaskRepository;

class PublishTaskService
{
    public function __construct(
        private readonly CityRepository $cityRepository,
        private readonly TaskRepository $taskRepository,
    ) {
    }

    public function publish(CreateTaskDTO $task)
    {
        $city = $this->cityRepository->getCityById($task->cityId);


        $this->taskRepository->save($task);
    }
}
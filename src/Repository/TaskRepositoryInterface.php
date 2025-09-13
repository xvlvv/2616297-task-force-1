<?php

namespace Xvlvv\Repository;

use Xvlvv\DTO\SaveTaskDTO;
use Xvlvv\Entity\Task;

interface TaskRepositoryInterface
{
    public function save(SaveTaskDTO $taskDTO): ?int;
    public function update(Task $task): void;
    public function hasAlreadyResponded(int $taskId, int $userId): bool;
    public function isAuthor(int $taskId, int $userId): bool;
    public function isWorker(int $taskId, int $userId): bool;
    public function getWorkerByIdOrFail(int $taskId): int;
    public function getByIdOrFail(int $taskId): Task;
    public function getNewTasks(int $offset, int $limit): array;
}
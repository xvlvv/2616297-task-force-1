<?php

declare(strict_types = 1);

namespace Xvlvv\Repository;

use Xvlvv\Entity\TaskResponse;

/**
 * Интерфейс репозитория для откликов на задачи
 */
interface TaskResponseRepositoryInterface
{
    /**
     * Сохраняет отклик на задачу
     * @param TaskResponse $taskResponse
     * @return bool
     */
    public function save(TaskResponse $taskResponse): bool;

    /**
     * Сохраняет обновлённый отклик на задачу
     * @param TaskResponse $taskResponse
     * @return bool
     */
    public function update(TaskResponse $taskResponse): bool;

    public function getByIdOrFail(int $id): TaskResponse;

    public function getTaskIdByResponseId(int $id): int;

    /**
     * Находит и формирует DTO откликов для страницы просмотра задачи
     * @param int $id ID задачи
     * @param int $currentUserId
     * @return array
     */
    public function findByTaskId(int $id, int $currentUserId): array;
}
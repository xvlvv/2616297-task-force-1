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
}
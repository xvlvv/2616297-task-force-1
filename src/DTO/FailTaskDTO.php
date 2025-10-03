<?php

declare(strict_types=1);

namespace Xvlvv\DTO;

/**
 * DTO для провала задания
 */
readonly final class FailTaskDTO
{
    /**
     * @param int $userId ID пользователя, который проваливает задание
     * @param int $taskId ID задания
     */
    public function __construct(
        public int $userId,
        public int $taskId,
    ) {
    }
}
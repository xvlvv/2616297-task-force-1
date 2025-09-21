<?php

declare(strict_types = 1);

namespace Xvlvv\DTO;

/**
 * DTO для отмены задания
 */
readonly final class CancelTaskDTO
{
    /**
     * @param int $taskId ID задания
     * @param int $userId ID автора задания
     */
    public function __construct(
        public int $taskId,
        public int $userId,
    ) {
    }
}
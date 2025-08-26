<?php

namespace Xvlvv\DTO;

readonly final class CancelTaskDTO
{
    public function __construct(
        public int $taskId,
        public int $userId,
    ) {
    }
}
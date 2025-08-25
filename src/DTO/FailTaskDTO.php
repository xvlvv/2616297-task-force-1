<?php

namespace Xvlvv\DTO;

readonly final class FailTaskDTO
{
    public function __construct(
        public int $userId,
        public int $taskId,
    ) {
    }
}
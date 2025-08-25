<?php

namespace Xvlvv\DTO;

readonly final class StartTaskDTO
{
    public function __construct(
        public int $taskId,
        public int $customerId,
        public int $workerId,
    ) {
    }
}
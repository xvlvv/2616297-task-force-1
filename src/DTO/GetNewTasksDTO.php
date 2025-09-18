<?php

declare(strict_types = 1);

namespace Xvlvv\DTO;

readonly final class GetNewTasksDTO
{
    public function __construct(
        public string|array $categories,
        public bool $checkWorker,
        public string $createdAt,
        public int $offset = 0,
        public int $limit = 0,
    ) {
    }
}
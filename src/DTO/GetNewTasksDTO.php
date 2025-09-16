<?php

declare(strict_types = 1);

namespace Xvlvv\DTO;

readonly final class GetNewTasksDTO
{
    public function __construct(
        public int $offset,
        public int $limit,
        public string|array $categories,
        public bool $checkWorker,
        public string $createdAt,
    ) {
    }
}
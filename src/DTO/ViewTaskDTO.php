<?php

namespace Xvlvv\DTO;

readonly final class ViewTaskDTO
{
    public function __construct(
        public string $name,
        public int $budget,
        public string $description,
        public string $category,
        public string $createdAt,
        public ?string $endDate,
        public string $status,
        public array $responses,
    ) {
    }
}
<?php

namespace Xvlvv\DTO;

readonly final class RenderTaskDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public string $category,
        public string $city,
        public int $budget,
        public string $createdAt,
    ) {
    }
}
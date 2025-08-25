<?php

namespace Xvlvv\DTO;

readonly final class CreateTaskDTO
{
    public function __construct(
        public string $name,
        public string $description,
        public int $categoryId,
        public int $customerId,
        public ?\DateTimeImmutable $endDate = null,
        public ?string $latitude = null,
        public ?string $longitude = null,
        public ?int $budget = null,
        public ?int $cityId = null,
        public array $fileIds = []
    ) {
    }
}
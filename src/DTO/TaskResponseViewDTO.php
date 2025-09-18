<?php

namespace Xvlvv\DTO;

class TaskResponseViewDTO
{
    public function __construct(
        public string $workerName,
        public float $rating,
        public int $reviewCount,
        public ?string $avatarPath,
        public string $createdAt,
        public string $comment,
        public int $price
    ) {
    }
}
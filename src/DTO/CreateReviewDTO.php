<?php

namespace Xvlvv\DTO;

readonly final class CreateReviewDTO
{
    public function __construct(
        public int $taskId,
        public int $authorId,
        public int $workerId,
        public string $comment,
        public ?int $rating = null,
    ) {
    }
}
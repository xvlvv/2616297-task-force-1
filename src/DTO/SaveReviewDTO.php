<?php

namespace Xvlvv\DTO;

readonly final class SaveReviewDTO
{
    public function __construct(
        public int $rating,
        public string $comment,
        public int $taskId,
        public int $authorId,
    ) {
    }
}
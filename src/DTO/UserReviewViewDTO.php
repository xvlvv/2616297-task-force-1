<?php

namespace Xvlvv\DTO;

readonly final class UserReviewViewDTO
{
    public function __construct(
        public string $taskName,
        public string $comment,
        public int $rating,
        public string $createdAt,
        public string $avatarPath,
    ) {
    }
}
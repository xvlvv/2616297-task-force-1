<?php

namespace Xvlvv\DTO;

readonly final class ViewUserDTO
{
    public function __construct(
        public string $name,
        public ?string $avatarPath,
        public string $description,
        public string $bio,
        public array $specializations,
        public int $completedTasks,
        public int $failedTasks,
        public float $rating,
        public int $ratingPlacement,
        public string $createdAt,
        public string $status,
        public string $phoneNumber,
        public string $email,
        public string $telegramUsername,
        public bool $showContacts,
        public array $reviews,
    ) {
    }
}
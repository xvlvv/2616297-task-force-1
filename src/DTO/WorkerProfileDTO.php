<?php

namespace Xvlvv\DTO;

readonly final class WorkerProfileDTO implements ProfileDataInterface
{
    public function __construct(
        public int $failedTasksCount,
        public bool $showContactsOnlyToCustomers,
        public ?string $dayOfBirth = null,
        public ?string $bio = null,
        public ?string $phoneNumber = null,
        public ?string $telegramUsername = null,
    ) {
    }
}
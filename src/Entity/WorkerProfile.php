<?php

namespace Xvlvv\Entity;

use Xvlvv\DTO\WorkerProfileDTO;

class WorkerProfile implements UserProfileInterface
{
    private int $failedTasksCount;
    private bool $showContactsOnlyToCustomers;
    private ?string $dayOfBirth;
    private ?string $bio;
    private ?string $phoneNumber;
    private ?string $telegramUsername;
    public function __construct(
        WorkerProfileDTO $dto
    ) {
        $this->failedTasksCount = $dto->failedTasksCount;
        $this->showContactsOnlyToCustomers = $dto->showContactsOnlyToCustomers;
        $this->dayOfBirth = $dto->dayOfBirth;
        $this->bio = $dto->bio;
        $this->phoneNumber = $dto->phoneNumber;
        $this->telegramUsername = $dto->telegramUsername;
    }

    public function getFailedTasksCount(): int
    {
        return $this->failedTasksCount;
    }

    public function incrementFailedTasksCount(): void
    {
        $this->failedTasksCount++;
    }

    public function isShowContactsOnlyToCustomers(): bool
    {
        return $this->showContactsOnlyToCustomers;
    }

    public function getDayOfBirth(): ?string
    {
        return $this->dayOfBirth;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function getTelegramUsername(): ?string
    {
        return $this->telegramUsername;
    }
}
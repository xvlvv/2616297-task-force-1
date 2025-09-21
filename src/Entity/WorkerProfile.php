<?php

declare(strict_types = 1);

namespace Xvlvv\Entity;

use Xvlvv\DTO\WorkerProfileDTO;

/**
 * Профиль пользователя-исполнителя
 */
class WorkerProfile implements UserProfileInterface
{
    private int $failedTasksCount;
    private bool $showContactsOnlyToCustomers;
    private ?string $dayOfBirth;
    private ?string $bio;
    private ?string $phoneNumber;
    private ?string $telegramUsername;

    /**
     * Конструктор WorkerProfile
     * @param WorkerProfileDTO $dto
     */
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

    /**
     * @return int
     */
    public function getFailedTasksCount(): int
    {
        return $this->failedTasksCount;
    }

    /**
     * Увеличивает счетчик проваленных задач на единицу
     */
    public function incrementFailedTasksCount(): void
    {
        $this->failedTasksCount++;
    }

    /**
     * @return bool
     */
    public function isShowContactsOnlyToCustomers(): bool
    {
        return $this->showContactsOnlyToCustomers;
    }

    /**
     * @return string|null
     */
    public function getDayOfBirth(): ?string
    {
        return $this->dayOfBirth;
    }

    /**
     * @return string|null
     */
    public function getBio(): ?string
    {
        return $this->bio;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }


    /**
     * @return string|null
     */
    public function getTelegramUsername(): ?string
    {
        return $this->telegramUsername;
    }
}
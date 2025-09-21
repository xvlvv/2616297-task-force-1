<?php

namespace Xvlvv\Entity;

use LogicException;

class User
{
    public function __construct(
        private string $name,
        private string $email,
        private ?string $password_hash,
        private readonly UserRoleInterface $userRole,
        private readonly UserProfileInterface $profile,
        private City $city,
        private ?string $avatarPath = null,
        private ?int $id = null,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->password_hash;
    }

    public function getUserRole(): string
    {
        return $this->userRole->getRole();
    }

    public function isValidPassword(string $password): bool
    {
        return password_verify($password, $this->password_hash);
    }

    public function increaseFailedTasksCount(): void
    {
        $this->userRole->increaseFailedTasksCount($this->profile);
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function setId(int $id): void
    {
        if (null !== $this->getId()) {
            throw new LogicException('Нельзя обновить уже существующий идентификатор');
        }

        $this->id = $id;
    }

    public function getFailedTasksCount(): int
    {
        $this->userRole->getFailedTasksCount($this->profile);
    }

    public function canCreateTask(): bool
    {
        return $this->userRole->canCreateTask();
    }

    public function canApplyToTask(): bool
    {
        return $this->userRole->canApplyToTask();
    }
}
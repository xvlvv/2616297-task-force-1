<?php

namespace Xvlvv\Entity;

use Xvlvv\Entity\UserRoleInterface;

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

    public function getId(): int
    {
        return $this->id;
    }

    public function isValidPassword(string $password): bool
    {
        return password_verify($password, $this->password_hash);
    }

    public function increaseFailedTasksCount(): void
    {
        $this->userRole->increaseFailedTasksCount($this->profile);
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
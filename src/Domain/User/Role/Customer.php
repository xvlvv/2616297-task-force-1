<?php

namespace Xvlvv\Domain\User\Role;

use Xvlvv\Entity\UserRoleInterface;
use Xvlvv\Entity\UserProfileInterface;
use Xvlvv\Enums\UserRole;

final class Customer implements UserRoleInterface
{
    public function canApplyToTask(): bool
    {
        return false;
    }

    public function canCreateTask(): bool
    {
        return true;
    }

    public function getFailedTasksCount(UserProfileInterface $profile): int
    {
        throw new \DomainException('Customer cannot have tasks.');
    }

    public function increaseFailedTasksCount(UserProfileInterface $profile): void
    {
        throw new \DomainException('Customer cannot have tasks.');
    }

    public function getRole(): string
    {
        return UserRole::CUSTOMER->value;
    }
}
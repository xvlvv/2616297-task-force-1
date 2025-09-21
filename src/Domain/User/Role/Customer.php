<?php

declare(strict_types = 1);

namespace Xvlvv\Domain\User\Role;

use Xvlvv\Entity\UserRoleInterface;
use Xvlvv\Entity\UserProfileInterface;
use Xvlvv\Enums\UserRole;

final class Customer implements UserRoleInterface
{
    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public function canApplyToTask(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public function canCreateTask(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @return int
     */
    public function getFailedTasksCount(UserProfileInterface $profile): int
    {
        throw new \DomainException('Customer cannot have tasks.');
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function increaseFailedTasksCount(UserProfileInterface $profile): void
    {
        throw new \DomainException('Customer cannot have tasks.');
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getRole(): string
    {
        return UserRole::CUSTOMER->value;
    }
}
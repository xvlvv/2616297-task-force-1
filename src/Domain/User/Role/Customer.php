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
     * @return string
     */
    public function getRole(): UserRole
    {
        return UserRole::CUSTOMER;
    }
}
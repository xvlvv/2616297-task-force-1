<?php

declare(strict_types = 1);

namespace Xvlvv\Domain\User\Role;

use Xvlvv\Entity\UserProfileInterface;
use Xvlvv\Entity\UserRoleInterface;
use Xvlvv\Entity\WorkerProfile;
use Xvlvv\Enums\UserRole;

class Worker implements UserRoleInterface
{
    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public function canApplyToTask(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public function canCreateTask(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getRole(): UserRole
    {
        return UserRole::WORKER;
    }
}
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
     * @return int
     */
    public function getFailedTasksCount(UserProfileInterface $profile): int
    {
        if (!$profile instanceof WorkerProfile) {
            throw new \LogicException('Profile should be an instance of WorkerProfile');
        }

        return $profile->getFailedTasksCount();
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function increaseFailedTasksCount(UserProfileInterface $profile): void
    {
        if (!$profile instanceof WorkerProfile) {
            throw new \LogicException('Profile should be an instance of WorkerProfile');
        }

        $profile->incrementFailedTasksCount();
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getRole(): string
    {
        return UserRole::WORKER->value;
    }
}
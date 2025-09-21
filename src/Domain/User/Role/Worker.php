<?php

namespace Xvlvv\Domain\User\Role;

use Xvlvv\Entity\UserProfileInterface;
use Xvlvv\Entity\UserRoleInterface;
use Xvlvv\Entity\WorkerProfile;
use Xvlvv\Enums\UserRole;

class Worker implements UserRoleInterface
{
    public function canApplyToTask(): bool
    {
        return true;
    }

    public function canCreateTask(): bool
    {
        return false;
    }

    public function getFailedTasksCount(UserProfileInterface $profile): int
    {
        if (!$profile instanceof WorkerProfile) {
            throw new \LogicException('Profile should be an instance of WorkerProfile');
        }

        return $profile->getFailedTasksCount();
    }

    public function increaseFailedTasksCount(UserProfileInterface $profile): void
    {
        if (!$profile instanceof WorkerProfile) {
            throw new \LogicException('Profile should be an instance of WorkerProfile');
        }

        $profile->incrementFailedTasksCount();
    }

    public function getRole(): string
    {
        return UserRole::WORKER->value;
    }
}
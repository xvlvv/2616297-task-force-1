<?php

namespace Xvlvv\Entity;

interface UserRoleInterface
{
    public function canApplyToTask(): bool;
    public function canCreateTask(): bool;
    public function getFailedTasksCount(UserProfileInterface $profile): int;
    public function increaseFailedTasksCount(UserProfileInterface $profile): void;
}
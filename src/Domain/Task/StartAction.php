<?php

namespace Xvlvv\Domain\Task;

use Xvlvv\Exception\PermissionDeniedException;

class StartAction extends Action
{
    public function getName(): string
    {
        return $this->name;
    }

    public function getInternalName(): string
    {
        return $this->internalName;
    }

    public function canMakeAction(int $userId, int $authorId, ?int $workerId = null): bool
    {
        return $userId === $authorId;
    }
}
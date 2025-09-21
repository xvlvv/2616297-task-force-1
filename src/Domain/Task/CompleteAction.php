<?php

declare(strict_types = 1);

namespace Xvlvv\Domain\Task;

class CompleteAction extends Action
{
    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getInternalName(): string
    {
        return $this->internalName;
    }

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public function canMakeAction(int $userId, int $authorId, ?int $workerId = null): bool
    {
        return $userId === $authorId;
    }
}
<?php

namespace Xvlvv\Domain\Task;

abstract class Action
{
    public function __construct(
        protected string $name,
        protected string $internalName,
    )
    {
    }
    public abstract function getName(): string;
    public abstract function getInternalName(): string;
    public abstract function canMakeAction(int $userId, int $authorId, ?int $workerId = null): bool;
}
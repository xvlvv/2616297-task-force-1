<?php

namespace Xvlvv\Entity;

final class TaskResponse
{
    public function __construct(
        private int $id,
        private Task $task,
        private User $user,
        private bool $isRejected,
        private ?string $comment = null,
    ) {
    }
}
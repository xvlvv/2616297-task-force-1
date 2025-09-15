<?php

namespace Xvlvv\Entity;

use Yii;

final class TaskResponse
{
    private function __construct(
        private int $id,
        private Task $task,
        private User $user,
        private bool $isRejected,
        private int $price,
        private ?string $comment = null,
    ) {
    }

    public static function create(
        int $id,
        Task $task,
        User $user,
        bool $isRejected,
        int $price,
        ?string $comment = null,
    ): TaskResponse
    {
        $taskBudget = $task->getBudget();

        if ((int) $taskBudget < $price) {
            throw new \LogicException('Price cannot be higher than budget');
        }

        return new self(
            $id,
            $task,
            $user,
            $isRejected,
            $price,
            $comment
        );
    }

    public function getTaskId(): ?int
    {
        return $this->task->getId();
    }

    public function getWorkerId(): ?int
    {
        return $this->user->getId();
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function isRejected(): bool
    {
        return $this->isRejected;
    }
}
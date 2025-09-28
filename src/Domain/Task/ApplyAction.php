<?php

declare(strict_types = 1);

namespace Xvlvv\Domain\Task;

use Xvlvv\Repository\TaskRepositoryInterface;
use Yii;

class ApplyAction extends Action
{
    private TaskRepositoryInterface $taskRepository;

    public function __construct(
        protected string $name,
        protected string $internalName,
    )
    {
        $this->taskRepository = Yii::$container->get(TaskRepositoryInterface::class);
        parent::__construct($this->name, $this->internalName);
    }

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
    public function canMakeAction(int $userId, int $authorId, ?int $workerId = null, ?int $taskId = null): bool
    {
        $isNotAuthor = $userId !== $authorId;

        if ($taskId === null) {
            return false; // Не можем проверить без ID задания
        }

        $hasNotResponded = !$this->taskRepository->hasAlreadyResponded($taskId, $userId);

        return $isNotAuthor && $hasNotResponded;
    }
}
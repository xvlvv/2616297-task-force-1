<?php

declare(strict_types = 1);

namespace Xvlvv\Entity;

/**
 * Сущность Отклик на задание
 */
final class TaskResponse
{
    /**
     * Конструктор TaskResponse
     */
    private function __construct(
        private int $id,
        private Task $task,
        private User $user,
        private bool $isRejected,
        private int $price,
        private ?string $comment = null,
    ) {
    }


    /**
     * Фабричный метод для создания отклика
     *
     * @param int $id ID отклика
     * @param Task $task Сущность задания
     * @param User $user Сущность пользователя
     * @param bool $isRejected Флаг, отклонен ли отклик
     * @param int $price Предложенная цена
     * @param string|null $comment Комментарий
     * @return TaskResponse
     * @throws \LogicException если цена выше бюджета
     */
    public static function create(
        int $id,
        Task $task,
        User $user,
        bool $isRejected,
        int $price,
        ?string $comment = null,
    ): TaskResponse
    {
        return new self(
            $id,
            $task,
            $user,
            $isRejected,
            $price,
            $comment
        );
    }

    /**
     * @return int|null
     */
    public function getTaskId(): ?int
    {
        return $this->task->getId();
    }

    /**
     * @return int|null
     */
    public function getWorkerId(): ?int
    {
        return $this->user->getId();
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @return bool
     */
    public function isRejected(): bool
    {
        return $this->isRejected;
    }
}
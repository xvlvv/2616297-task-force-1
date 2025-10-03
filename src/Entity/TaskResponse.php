<?php

declare(strict_types = 1);

namespace Xvlvv\Entity;

use LogicException;

/**
 * Сущность Отклик на задание
 */
final class TaskResponse
{
    /**
     * @param int $id ID отклика
     * @param Task $task Сущность связанного задания
     * @param User $user Сущность пользователя-исполнителя
     * @param bool $isRejected Флаг, отклонен ли отклик
     * @param int|null $price Предложенная стоимость
     * @param string|null $comment Комментарий к отклику
     */
    private function __construct(
        private int $id,
        private Task $task,
        private User $user,
        private bool $isRejected,
        private ?int $price = null,
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
     * @param int|null $price Предложенная цена
     * @param string|null $comment Комментарий
     * @return TaskResponse
     */
    public static function create(
        int $id,
        Task $task,
        User $user,
        bool $isRejected,
        ?int $price = null,
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
     * Возвращает ID отклика.
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Возвращает ID связанного задания.
     * @return int|null
     */
    public function getTaskId(): ?int
    {
        return $this->task->getId();
    }

    /**
     * Возвращает ID исполнителя, оставившего отклик.
     * @return int|null
     */
    public function getWorkerId(): ?int
    {
        return $this->user->getId();
    }

    /**
     * Возвращает комментарий к отклику.
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Отклоняет отклик.
     * @return void
     * @throws LogicException если отклик уже отклонен.
     */
    public function reject(): void
    {
        if ($this->isRejected) {
            throw new LogicException('Cannot reject already rejected task response.');
        }
        $this->isRejected = true;
    }

    /**
     * Возвращает предложенную стоимость.
     * @return int|null
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * Проверяет, отклонен ли отклик.
     * @return bool
     */
    public function isRejected(): bool
    {
        return $this->isRejected;
    }
}
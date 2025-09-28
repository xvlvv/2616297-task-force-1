<?php

declare(strict_types = 1);

namespace Xvlvv\Domain\Task;

/**
 * Абстрактный базовый класс для действия над задачей.
 *
 * Определяет общую структуру и контракт для различных действий,
 * которые могут быть выполнены над задачей (например, "Ответить", "Отменить", "Завершить").
 */
abstract class Action
{
    /**
     * Конструктор класса Action
     *
     * @param string $name Публичное имя действия
     * @param string $internalName Внутреннее имя действия
     */
    public function __construct(
        protected string $name,
        protected string $internalName,
    ) {
    }

    /**
     * Возвращает публичное имя действия
     *
     * @return string
     */
    public abstract function getName(): string;

    /**
     * Возвращает внутреннее, действия.
     *
     * @return string
     */
    public abstract function getInternalName(): string;

    /**
     * Проверяет, имеет ли указанный пользователь право выполнить это действие,
     * проверка основана на роли пользователя по отношению к задаче
     *
     * @param int $userId ID пользователя, который пытается выполнить действие
     * @param int $authorId ID автора задачи
     * @param int|null $workerId ID исполнителя задачи, если он назначен
     * @return bool true, если пользователь может выполнить действие, иначе false
     */
    public abstract function canMakeAction(int $userId, int $authorId, ?int $workerId = null, ?int $taskId = null): bool;
}
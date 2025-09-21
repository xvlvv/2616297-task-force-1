<?php

declare(strict_types = 1);

namespace Xvlvv\Entity;

use DomainException;
use LogicException;

/**
 * Интерфейс для определения роли пользователя в системе
 */
interface UserRoleInterface
{
    /**
     * Возвращает строковый идентификатор роли
     *
     * @return string Имя роли (например, 'worker' или 'customer')
     */
    public function getRole(): string;


    /**
     * Проверяет, может ли пользователь с этой ролью откликаться на задачи (быть исполнителем)
     *
     * @return bool true, если пользователь может быть исполнителем, иначе false
     */
    public function canApplyToTask(): bool;

    /**
     * Проверяет, может ли пользователь с этой ролью создавать задачи (быть заказчиком)
     *
     * @return bool true, если пользователь может быть заказчиком, иначе false
     */
    public function canCreateTask(): bool;

    /**
     * Получает количество проваленных задач для данного профиля пользователя
     *
     * @param UserProfileInterface $profile Профиль пользователя
     * @return int Количество проваленных задач
     * @throws LogicException|DomainException Если операция неприменима к данной роли
     */
    public function getFailedTasksCount(UserProfileInterface $profile): int;

    /**
     * Увеличивает счетчик проваленных задач для данного профиля пользователя
     *
     * @param UserProfileInterface $profile Профиль пользователя
     * @return void
     * @throws LogicException|DomainException Если операция неприменима к данной роли
     */
    public function increaseFailedTasksCount(UserProfileInterface $profile): void;
}
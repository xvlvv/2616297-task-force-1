<?php

declare(strict_types = 1);

namespace Xvlvv\Entity;

use DomainException;
use LogicException;
use Xvlvv\Enums\UserRole;

/**
 * Интерфейс для определения роли пользователя в системе
 */
interface UserRoleInterface
{
    /**
     * Возвращает строковый идентификатор роли
     *
     * @return UserRole Имя роли (например, 'worker' или 'customer')
     */
    public function getRole(): UserRole;


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
}
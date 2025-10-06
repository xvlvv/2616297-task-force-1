<?php

declare(strict_types=1);

namespace Xvlvv\Repository;

use Xvlvv\DTO\ViewUserDTO;
use Xvlvv\Entity\User;

/**
 * Интерфейс репозитория для пользователей
 */
interface UserRepositoryInterface
{
    /**
     * Находит пользователя по ID
     *
     * @param int $id
     * @return User|null
     */
    public function getById(int $id): ?User;

    /**
     * Находит пользователя по ID или выбрасывает исключение
     *
     * @param int $id
     * @return User
     */
    public function getByIdOrFail(int $id): User;

    /**
     * Находит пользователя по email или выбрасывает исключение
     *
     * @param string $email
     * @return User
     */
    public function getByEmailOrFail(string $email): User;

    /**
     * Находит пользователя по email
     *
     * @param string|null $email
     * @return User|null
     */
    public function getByEmail(?string $email): ?User;

    /**
     * Обновляет данные пользователя
     *
     * @param User $user
     * @return void
     */
    public function update(User $user): void;

    /**
     * Сохраняет пользователя и возвращает сущность с ID
     *
     * @param User $user
     * @return User
     */
    public function save(User $user): User;

    /**
     * Проверяет существование пользователя по email
     *
     * @param string $email
     * @return bool
     */
    public function isUserExistsByEmail(string $email): bool;

    /**
     * Получает данные для страницы просмотра профиля исполнителя
     *
     * @param int $id
     * @return ViewUserDTO
     */
    public function getWorkerForView(int $id): ViewUserDTO;

    /**
     * Вычисляет место пользователя в рейтинге
     *
     * @param int $userId
     * @return int
     */
    public function getUserRank(int $userId): int;

    /**
     * Находит пользователя по его VK ID
     *
     * @param int $vkId ID пользователя ВКонтакте
     * @return User|null Возвращает сущность User, если пользователь найден, иначе null
     */
    public function getByVkId(int $vkId): ?User;
}
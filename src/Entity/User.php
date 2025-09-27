<?php

declare(strict_types = 1);

namespace Xvlvv\Entity;

use LogicException;
use Xvlvv\Enums\UserRole;
use Yii;

/**
 * Сущность Пользователь
 */
class User
{
    /**
     * Конструктор User
     *
     * @param string $name Имя
     * @param string|null $email Email
     * @param string|null $password_hash Хеш пароля
     * @param UserRoleInterface $userRole Роль пользователя
     * @param UserProfileInterface $profile Профиль пользователя
     * @param City $city Город
     * @param string|null $accessToken
     * @param string|null $avatarPath Путь к аватару
     * @param int|null $id ID пользователя
     */
    public function __construct(
        private string $name,
        private ?string $email,
        private ?string $password_hash,
        private readonly UserRoleInterface $userRole,
        private readonly UserProfileInterface $profile,
        private City $city,
        private readonly ?string $accessToken,
        private ?string $avatarPath = null,
        private ?int $id = null,
    ) {
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->password_hash;
    }

    public function getAvatarPath(): ?string
    {
        return $this->avatarPath;
    }

    public function getProfile(): UserProfileInterface
    {
        return $this->profile;
    }

    /**
     * @return string
     */
    public function getUserRole(): UserRole
    {
        return $this->userRole->getRole();
    }

    /**
     * Проверяет корректность пароля
     *
     * @param string $password
     * @return bool
     */
    public function isValidPassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * @return City
     */
    public function getCity(): City
    {
        return $this->city;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * Устанавливает ID для нового пользователя
     *
     * @param int $id
     * @throws LogicException
     */
    public function setId(int $id): void
    {
        if (null !== $this->getId()) {
            throw new LogicException('Нельзя обновить уже существующий идентификатор');
        }

        $this->id = $id;
    }

    /**
     * Проверяет право на создание задания через объект роли
     * @return bool
     */
    public function canCreateTask(): bool
    {
        return $this->userRole->canCreateTask();
    }

    /**
     * Проверяет право откликаться на задание через объект роли
     * @return bool
     */
    public function canApplyToTask(): bool
    {
        return $this->userRole->canApplyToTask();
    }
}
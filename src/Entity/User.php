<?php

declare(strict_types = 1);

namespace Xvlvv\Entity;

use LogicException;
use RuntimeException;
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
     * @param string|null $passwordHash Хеш пароля
     * @param UserRoleInterface $userRole Роль пользователя
     * @param UserProfileInterface $profile Профиль пользователя
     * @param City $city Город
     * @param string|null $accessToken
     * @param string|null $avatarPath Путь к аватару
     * @param int|null $id ID пользователя
     * @param int|null $vkId
     */
    public function __construct(
        private string $name,
        private ?string $email,
        private ?string $passwordHash,
        private readonly UserRoleInterface $userRole,
        private readonly UserProfileInterface $profile,
        private City $city,
        private readonly ?string $accessToken,
        private ?string $avatarPath = null,
        private ?int $id = null,
        private ?int $vkId = null,
    ) {
    }

    /**
     * Возвращает ID пользователя.
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Возвращает имя пользователя.
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Возвращает email пользователя.
     * @return string|null
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Привязывает VK ID к существующему пользователю.
     * @param int $vkId
     * @return void
     * @throws RuntimeException если VK ID уже привязан.
     */
    public function updateWithVkId(int $vkId): void
    {
        if (null !== $this->getVkId()) {
            throw new RuntimeException('VK ID already exists');
        }

        $this->vkId = $vkId;
    }

    /**
     * Возвращает VK ID пользователя.
     * @return int|null
     */
    public function getVkId(): ?int
    {
        return $this->vkId;
    }

    /**
     * Возвращает хеш пароля.
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * Возвращает путь к аватару.
     * @return string|null
     */
    public function getAvatarPath(): ?string
    {
        return $this->avatarPath;
    }

    /**
     * Возвращает объект профиля пользователя (WorkerProfile или CustomerProfile).
     * @return UserProfileInterface
     */
    public function getProfile(): UserProfileInterface
    {
        return $this->profile;
    }

    /**
     * Возвращает enum роли пользователя.
     * @return UserRole
     */
    public function getUserRole(): UserRole
    {
        return $this->userRole->getRole();
    }

    /**
     * Проверяет, соответствует ли переданный пароль хешу.
     *
     * @param string $password Пароль в открытом виде для проверки.
     * @return bool
     */
    public function isValidPassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->passwordHash);
    }

    /**
     * Изменяет имя пользователя.
     * @param string $name
     * @return void
     */
    public function changeName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Изменяет email пользователя.
     * @param string $email
     * @return void
     */
    public function changeEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Изменяет путь к аватару.
     * @param string $avatarPath
     * @return void
     */
    public function changeAvatar(string $avatarPath): void
    {
        $this->avatarPath = $avatarPath;
    }

    /**
     * Изменяет хеш пароля.
     * @param string $newPasswordHash Новый хеш пароля.
     * @return void
     */
    public function changePassword(string $newPasswordHash): void
    {
        $this->passwordHash = $newPasswordHash;
    }

    /**
     * Возвращает сущность города пользователя.
     * @return City
     */
    public function getCity(): City
    {
        return $this->city;
    }

    /**
     * Возвращает токен доступа (Auth Key).
     * @return string|null
     */
    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * Устанавливает ID для новой сущности пользователя.
     * Защищает от перезаписи существующего ID.
     *
     * @param int $id
     * @throws LogicException если ID уже установлен.
     */
    public function setId(int $id): void
    {
        if (null !== $this->getId() && $id !== $this->getId()) {
            throw new LogicException('Нельзя обновить уже существующий идентификатор');
        }

        $this->id = $id;
    }

    /**
     * Проверяет, может ли пользователь создавать задания.
     * Делегирует проверку объекту роли.
     * @return bool
     */
    public function canCreateTask(): bool
    {
        return $this->userRole->canCreateTask();
    }

    /**
     * Проверяет, может ли пользователь откликаться на задания.
     * Делегирует проверку объекту роли.
     * @return bool
     */
    public function canApplyToTask(): bool
    {
        return $this->userRole->canApplyToTask();
    }
}
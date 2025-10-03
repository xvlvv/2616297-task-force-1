<?php

declare(strict_types=1);

namespace Xvlvv\Entity;

use Xvlvv\DTO\WorkerProfileDTO;

/**
 * Профиль пользователя-исполнителя
 */
class WorkerProfile implements UserProfileInterface
{
    /** @var string|null Информация "О себе" */
    private ?string $bio;
    /** @var string|null Дата рождения */
    private ?string $dayOfBirth;
    /** @var string|null Номер телефона */
    private ?string $phoneNumber;
    /** @var bool Показывать контакты только заказчику */
    private bool $showContactsOnlyToCustomers;
    /** @var Category[] Массив сущностей категорий-специализаций */
    private array $specializations;
    /** @var string|null Имя пользователя в Telegram */
    private ?string $telegramUsername;

    /**
     * @param WorkerProfileDTO $dto DTO с данными для инициализации
     */
    public function __construct(
        WorkerProfileDTO $dto
    ) {
        $this->showContactsOnlyToCustomers = $dto->showContactsOnlyToCustomers;
        $this->dayOfBirth = $dto->dayOfBirth;
        $this->bio = $dto->bio;
        $this->phoneNumber = $dto->phoneNumber;
        $this->telegramUsername = $dto->telegramUsername;
        $this->specializations = $dto->specializations;
    }

    /**
     * Проверяет, скрыты ли контакты.
     * @return bool
     */
    public function isShowContactsOnlyToCustomers(): bool
    {
        return $this->showContactsOnlyToCustomers;
    }

    /**
     * Возвращает дату рождения.
     * @return string|null
     */
    public function getDayOfBirth(): ?string
    {
        return $this->dayOfBirth;
    }

    /**
     * Возвращает информацию "О себе".
     * @return string|null
     */
    public function getBio(): ?string
    {
        return $this->bio;
    }

    /**
     * Возвращает номер телефона.
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * Возвращает массив ID специализаций.
     * @return int[]
     */
    public function getSpecializationsIds(): array
    {
        return array_map(fn(Category $spec) => $spec->getId(), $this->getSpecializations());
    }

    /**
     * Возвращает массив сущностей категорий-специализаций.
     * @return Category[]
     */
    public function getSpecializations(): array
    {
        return $this->specializations;
    }

    /**
     * Возвращает имя пользователя в Telegram.
     * @return string|null
     */
    public function getTelegramUsername(): ?string
    {
        return $this->telegramUsername;
    }

    /**
     * Обновляет детали профиля.
     *
     * @param string|null $birthday Новая дата рождения
     * @param string|null $phone Новый номер телефона
     * @param string|null $telegram Новый юзернейм в Telegram
     * @param string|null $bio Новая информация "О себе"
     * @param Category[] $categories Массив новых сущностей категорий-специализаций
     */
    public function updateDetails(
        ?string $birthday,
        ?string $phone,
        ?string $telegram,
        ?string $bio,
        array $categories
    ): void {
        $this->dayOfBirth = $birthday;
        $this->phoneNumber = $phone;
        $this->telegramUsername = $telegram;
        $this->bio = $bio;
        $this->updateSpecializations($categories);
    }

    /**
     * Обновляет внутренний список категорий-специализаций.
     * @param array $specializations
     */
    private function updateSpecializations(array $specializations): void
    {
        $this->specializations = $specializations;
    }
}
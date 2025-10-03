<?php

declare(strict_types = 1);

namespace Xvlvv\DTO;

/**
 * DTO с данными профиля исполнителя
 */
readonly final class WorkerProfileDTO implements ProfileDataInterface
{
    /**
     * @param bool $showContactsOnlyToCustomers Флаг приватности контактов
     * @param string|null $dayOfBirth День рождения
     * @param string|null $bio Описание
     * @param string|null $phoneNumber Номер телефона
     * @param string|null $telegramUsername Имя в Telegram
     * @param array $specializations Массив специализаций исполнителя
     */
    public function __construct(
        public bool $showContactsOnlyToCustomers,
        public ?string $dayOfBirth = null,
        public ?string $bio = null,
        public ?string $phoneNumber = null,
        public ?string $telegramUsername = null,
        public array $specializations = [],
    ) {
    }
}
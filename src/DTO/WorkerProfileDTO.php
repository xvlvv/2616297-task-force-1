<?php

declare(strict_types = 1);

namespace Xvlvv\DTO;

/**
 * DTO с данными профиля исполнителя
 */
readonly final class WorkerProfileDTO implements ProfileDataInterface
{
    /**
     * @param int $failedTasksCount Счетчик проваленных заданий
     * @param bool $showContactsOnlyToCustomers Флаг приватности контактов
     * @param string|null $dayOfBirth День рождения
     * @param string|null $bio Описание
     * @param string|null $phoneNumber Номер телефона
     * @param string|null $telegramUsername Имя в Telegram
     */
    public function __construct(
        public int $failedTasksCount,
        public bool $showContactsOnlyToCustomers,
        public ?string $dayOfBirth = null,
        public ?string $bio = null,
        public ?string $phoneNumber = null,
        public ?string $telegramUsername = null,
    ) {
    }
}
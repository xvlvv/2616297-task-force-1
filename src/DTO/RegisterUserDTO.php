<?php

declare(strict_types = 1);

namespace Xvlvv\DTO;

/**
 * DTO для регистрации нового пользователя
 */
readonly final class RegisterUserDTO
{
    /**
     * @param string $name Имя пользователя
     * @param string $email Электронная почта
     * @param int $cityId ID города
     * @param string|null $password Пароль
     * @param bool $canApplyToTasks Флаг, может ли пользователь быть исполнителем
     */
    public function __construct(
        public string $name,
        public string $email,
        public int $cityId,
        public bool $canApplyToTasks,
        public ?string $password = null,
        public ?int $vkId = null,
        public ?string $avatar = null,
    ) {
    }
}
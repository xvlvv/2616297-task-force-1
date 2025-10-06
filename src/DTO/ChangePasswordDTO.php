<?php

declare(strict_types=1);

namespace Xvlvv\DTO;

/**
 * DTO для передачи данных о смене пароля.
 * Используется в SecurityService.
 */
final readonly class ChangePasswordDTO
{
    /**
     * @param string $newPassword Новый пароль пользователя в открытом виде.
     */
    public function __construct(
        public string $newPassword
    ) {
    }
}
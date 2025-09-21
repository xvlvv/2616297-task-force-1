<?php

declare(strict_types = 1);

namespace Xvlvv\DTO;

/**
 * DTO для специализации пользователя
 */
readonly final class UserSpecializationDTO
{
    /**
     * @param int $id ID специализации
     * @param string $name Название специализации
     */
    public function __construct(
        public int $id,
        public string $name,
    )
    {
    }
}
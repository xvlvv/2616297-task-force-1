<?php

namespace Xvlvv\DTO;

readonly final class RegisterUserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public int $cityId,
        public string $password,
        public bool $canApplyToTasks,
    ) {
    }
}
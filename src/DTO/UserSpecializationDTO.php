<?php

namespace Xvlvv\DTO;

readonly final class UserSpecializationDTO
{
    public function __construct(
        public int $id,
        public string $name,
    )
    {
    }
}
<?php

declare(strict_types = 1);

namespace Xvlvv\Factory;

use Xvlvv\DTO\ProfileDataInterface;
use Xvlvv\Entity\UserProfileInterface;

/**
 * Интерфейс для фабрик профилей пользователей
 */
interface UserProfileFactoryInterface
{
    /**
     * Создает сущность профиля из DTO
     *
     * @param ProfileDataInterface $dto
     * @return UserProfileInterface
     */
    public function createFromDTO(ProfileDataInterface $dto): UserProfileInterface;
}
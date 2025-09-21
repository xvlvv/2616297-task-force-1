<?php

declare(strict_types = 1);

namespace Xvlvv\Enums;

use Xvlvv\Domain\User\Role\Customer;
use Xvlvv\Domain\User\Role\Worker;
use Xvlvv\Entity\UserRoleInterface;
use Xvlvv\Factory\CustomerProfileFactory;
use Xvlvv\Factory\UserProfileFactoryInterface;
use Xvlvv\Factory\WorkerProfileFactory;

/**
 * Перечисление ролей пользователя
 */
enum UserRole: string
{
    case WORKER = 'worker';
    case CUSTOMER = 'customer';

    /**
     * Возвращает фабрику для создания профиля, соответствующего роли
     *
     * @return UserProfileFactoryInterface
     */
    public function getProfileFactory(): UserProfileFactoryInterface
    {
        return match ($this) {
            self::WORKER => new WorkerProfileFactory(),
            self::CUSTOMER => new CustomerProfileFactory(),
        };
    }


    /**
     * Создает и возвращает объект-роль
     *
     * @return UserRoleInterface
     */
    public function createRole(): UserRoleInterface
    {
        return match ($this) {
            self::WORKER => new Worker(),
            self::CUSTOMER => new Customer(),
        };
    }
}

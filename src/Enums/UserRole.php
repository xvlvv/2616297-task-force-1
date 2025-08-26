<?php

namespace Xvlvv\Enums;

use Xvlvv\Domain\User\Role\Customer;
use Xvlvv\Domain\User\Role\Worker;
use Xvlvv\DTO\CustomerProfileDTO;
use Xvlvv\DTO\WorkerProfileDTO;
use Xvlvv\Entity\CustomerProfile;
use Xvlvv\Entity\UserProfileInterface;
use Xvlvv\Entity\UserRoleInterface;
use Xvlvv\Entity\WorkerProfile;
use Xvlvv\Factory\CustomerProfileFactory;
use Xvlvv\Factory\UserProfileFactoryInterface;
use Xvlvv\Factory\WorkerProfileFactory;

enum UserRole: string
{
    case WORKER = 'worker';
    case CUSTOMER = 'customer';

    public function getProfileFactory(): UserProfileFactoryInterface
    {
        return match ($this) {
            self::WORKER => new WorkerProfileFactory(),
            self::CUSTOMER => new CustomerProfileFactory(),
        };
    }

    public function createRole(): UserRoleInterface
    {
        return match ($this) {
            self::WORKER => new Worker(),
            self::CUSTOMER => new Customer(),
        };
    }
}

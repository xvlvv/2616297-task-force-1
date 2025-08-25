<?php

namespace Xvlvv\Factory;

use Xvlvv\DTO\ProfileDataInterface;
use Xvlvv\DTO\WorkerProfileDTO;
use Xvlvv\Entity\UserProfileInterface;
use Xvlvv\Entity\WorkerProfile;
use Xvlvv\Factory\UserProfileFactoryInterface;

class WorkerProfileFactory implements UserProfileFactoryInterface
{

    public function createFromDTO(ProfileDataInterface $dto): UserProfileInterface
    {
        if (!$dto instanceof WorkerProfileDTO) {
            throw new \InvalidArgumentException('WorkerProfileFactory expects a WorkerProfileDTO.');
        }

        return new WorkerProfile($dto);
    }
}
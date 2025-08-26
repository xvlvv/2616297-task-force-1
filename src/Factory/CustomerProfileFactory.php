<?php

namespace Xvlvv\Factory;

use Xvlvv\DTO\CustomerProfileDTO;
use Xvlvv\DTO\ProfileDataInterface;
use Xvlvv\Entity\CustomerProfile;

class CustomerProfileFactory implements UserProfileFactoryInterface
{

    public function createFromDTO(ProfileDataInterface $dto): CustomerProfile
    {
        if (!$dto instanceof CustomerProfileDTO) {
            throw new \InvalidArgumentException('CustomerProfileFactory expects a CustomerProfileDTO.');
        }

        return new CustomerProfile();
    }
}
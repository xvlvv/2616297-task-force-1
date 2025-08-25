<?php

namespace Xvlvv\Factory;

use Xvlvv\DTO\ProfileDataInterface;
use Xvlvv\Entity\UserProfileInterface;

interface UserProfileFactoryInterface
{
    public function createFromDTO(ProfileDataInterface $dto): UserProfileInterface;
}
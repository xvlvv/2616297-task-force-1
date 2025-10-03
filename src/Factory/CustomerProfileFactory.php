<?php

declare(strict_types=1);

namespace Xvlvv\Factory;

use Xvlvv\DTO\CustomerProfileDTO;
use Xvlvv\DTO\ProfileDataInterface;
use Xvlvv\Entity\CustomerProfile;

/**
 * Фабрика для создания профиля заказчика
 */
class CustomerProfileFactory implements UserProfileFactoryInterface
{
    /**
     * Создает CustomerProfile из DTO
     *
     * @param ProfileDataInterface $dto DTO с данными, должен быть CustomerProfileDTO
     * @return CustomerProfile
     * @throws \InvalidArgumentException если DTO имеет неверный тип
     */
    public function createFromDTO(ProfileDataInterface $dto): CustomerProfile
    {
        if (!$dto instanceof CustomerProfileDTO) {
            throw new \InvalidArgumentException('CustomerProfileFactory expects a CustomerProfileDTO.');
        }

        return new CustomerProfile();
    }
}
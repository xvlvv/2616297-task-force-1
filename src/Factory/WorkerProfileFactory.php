<?php

declare(strict_types=1);

namespace Xvlvv\Factory;

use InvalidArgumentException;
use Xvlvv\DTO\ProfileDataInterface;
use Xvlvv\DTO\WorkerProfileDTO;
use Xvlvv\Entity\UserProfileInterface;
use Xvlvv\Entity\WorkerProfile;

/**
 * Фабрика для создания профиля исполнителя
 */
class WorkerProfileFactory implements UserProfileFactoryInterface
{
    /**
     * Создает WorkerProfile из DTO
     *
     * @param ProfileDataInterface $dto DTO с данными, должен быть WorkerProfileDTO
     * @return UserProfileInterface
     * @throws InvalidArgumentException если DTO имеет неверный тип
     */
    public function createFromDTO(ProfileDataInterface $dto): UserProfileInterface
    {
        if (!$dto instanceof WorkerProfileDTO) {
            throw new InvalidArgumentException('WorkerProfileFactory expects a WorkerProfileDTO.');
        }

        return new WorkerProfile($dto);
    }
}
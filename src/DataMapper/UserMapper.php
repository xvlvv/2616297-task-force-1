<?php

declare(strict_types = 1);

namespace Xvlvv\DataMapper;

use app\models\CustomerProfile as CustomerProfileModel;
use app\models\ExecutorProfile as WorkerProfileModel;
use app\models\User as ARUser;
use Xvlvv\DTO\CustomerProfileDTO;
use Xvlvv\DTO\WorkerProfileDTO;
use Xvlvv\Entity\User as DomainUser;
use Xvlvv\Entity\UserProfileInterface;
use Xvlvv\Enums\UserRole;
use Xvlvv\Factory\CustomerProfileFactory;
use Xvlvv\Factory\WorkerProfileFactory;

final class UserMapper
{
    public function __construct(
        private readonly CityMapper $cityMapper,
        private readonly WorkerProfileFactory $workerProfileFactory,
        private readonly CustomerProfileFactory $customerProfileFactory
    ) {
    }

    /**
     * Преобразует ActiveRecord модель в доменную сущность User.
     * @param ARUser $arUser ActiveRecord модель
     * @return DomainUser
     */
    public function toDomainEntity(ARUser $arUser): DomainUser
    {
        $userRoleEnum = UserRole::from($arUser->role);
        $userRole = $userRoleEnum->createRole();

        $profile = match ($userRoleEnum) {
            UserRole::CUSTOMER => $this->createCustomerProfile($arUser->customerProfile),
            UserRole::WORKER => $this->createWorkerProfile($arUser->workerProfile),
        };

        $city = $this->cityMapper->toDomainEntity($arUser->city);

        return new DomainUser(
            $arUser->name,
            $arUser->email,
            $arUser->password_hash,
            $userRole,
            $profile,
            $city,
            $arUser->access_token,
            $arUser->avatar_path ?? null,
            $arUser->id
        );
    }

    /**
     * Создает доменный объект WorkerProfile.
     * Сначала создает DTO из AR-модели, затем передает его в фабрику.
     * @param WorkerProfileModel|null $arProfile
     * @return UserProfileInterface
     */
    private function createWorkerProfile(?WorkerProfileModel $arProfile): UserProfileInterface
    {
        $dto = new WorkerProfileDTO(
            failedTasksCount: $arProfile->failed_tasks_count,
            showContactsOnlyToCustomers: (bool)$arProfile->restrict_contacts,
            dayOfBirth: $arProfile->day_of_birth,
            bio: $arProfile->bio,
            phoneNumber: $arProfile->phone_number,
            telegramUsername: $arProfile->telegram_username
        );

        return $this->workerProfileFactory->createFromDTO($dto);
    }

    /**
     * Создает доменный объект CustomerProfile.
     * @param CustomerProfileModel|null $arProfile
     * @return UserProfileInterface
     */
    private function createCustomerProfile(?CustomerProfileModel $arProfile): UserProfileInterface
    {
        return $this->customerProfileFactory->createFromDTO(new CustomerProfileDTO());
    }
}
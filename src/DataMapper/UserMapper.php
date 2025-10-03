<?php

declare(strict_types = 1);

namespace Xvlvv\DataMapper;

use app\models\CustomerProfile as CustomerProfileModel;
use app\models\ExecutorProfile as WorkerProfileModel;
use app\models\User as ARUser;
use app\models\UserSpecialization;
use Xvlvv\DTO\CustomerProfileDTO;
use Xvlvv\DTO\WorkerProfileDTO;
use Xvlvv\Entity\User as DomainUser;
use Xvlvv\Entity\UserProfileInterface;
use Xvlvv\Enums\UserRole;
use Xvlvv\Factory\CustomerProfileFactory;
use Xvlvv\Factory\WorkerProfileFactory;

/**
 * Маппер для преобразования данных между ActiveRecord моделью User и доменной сущностью User.
 */
final readonly class UserMapper
{
    /**
     * @param CityMapper $cityMapper Маппер для городов.
     * @param WorkerProfileFactory $workerProfileFactory Фабрика для профилей исполнителей.
     * @param CustomerProfileFactory $customerProfileFactory Фабрика для профилей заказчиков.
     * @param CategoryMapper $categoryMapper Маппер для категорий/специализаций.
     */
    public function __construct(
        private CityMapper $cityMapper,
        private WorkerProfileFactory $workerProfileFactory,
        private CustomerProfileFactory $customerProfileFactory,
        private CategoryMapper $categoryMapper,
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
            $arUser->id,
            $arUser->vk_id,
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
        if ($arProfile === null) {
            return $this->workerProfileFactory->createFromDTO(
                new WorkerProfileDTO(
                    showContactsOnlyToCustomers: false,
                )
            );
        }

        $userSpecializations = array_map(
            fn($categoryModel) => $this->categoryMapper->toDomainEntity($categoryModel->category),
            UserSpecialization::find()->where(['user_id' => $arProfile->user_id])->all()
        );

        $dto = new WorkerProfileDTO(
            showContactsOnlyToCustomers: (bool)$arProfile->restrict_contacts,
            dayOfBirth: $arProfile->day_of_birth,
            bio: $arProfile->bio,
            phoneNumber: $arProfile->phone_number,
            telegramUsername: $arProfile->telegram_username,
            specializations: $userSpecializations
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
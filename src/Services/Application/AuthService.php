<?php

namespace Xvlvv\Services\Application;

use Xvlvv\DTO\CustomerProfileDTO;
use Xvlvv\DTO\RegisterUserDTO;
use Xvlvv\DTO\WorkerProfileDTO;
use Xvlvv\Entity\User;
use Xvlvv\Enums\UserRole;
use Xvlvv\Exception\PermissionDeniedException;
use Xvlvv\Exception\UserWithEmailAlreadyExistsException;
use Xvlvv\Repository\CityRepositoryInterface;
use Xvlvv\Repository\UserRepositoryInterface;
use Yii;

final readonly class AuthService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private CityRepositoryInterface $cityRepository,
    ) {
    }

    public function authenticate(string $email, string $password): bool
    {
        $user = $this->userRepository->getByEmailOrFail($email);

        if (!$user->isValidPassword($password)) {
            throw new PermissionDeniedException('Password is incorrect');
        }

        return true;
    }

    public function register(RegisterUserDTO $dto): int
    {
        if ($this->userRepository->isUserExistsByEmail($dto->email)) {
            throw new UserWithEmailAlreadyExistsException();
        }

        $city = $this->cityRepository->getByIdOrFail($dto->cityId);
        $userRoleEnum = $dto->canApplyToTasks ? UserRole::WORKER : UserRole::CUSTOMER;
        $profileFactory = $userRoleEnum->getProfileFactory();
        $userRole = $userRoleEnum->createRole();

        $profileDTO = match ($userRoleEnum) {
            UserRole::CUSTOMER => new CustomerProfileDTO(),
            UserRole::WORKER => new WorkerProfileDTO(
                failedTasksCount: 0,
                showContactsOnlyToCustomers: true,
            ),
        };

        $profile = $profileFactory->createFromDTO($profileDTO);
        $passwordHash = Yii::$app->getSecurity()->generatePasswordHash($dto->password);

        $user = new User(
            $dto->name,
            $dto->email,
            $passwordHash,
            $userRole,
            $profile,
            $city
        );

        $user = $this->userRepository->save($user);

        return $user->getId();
    }
}
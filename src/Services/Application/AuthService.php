<?php

declare(strict_types = 1);

namespace Xvlvv\Services\Application;

use Xvlvv\DTO\CustomerProfileDTO;
use Xvlvv\DTO\RegisterUserDTO;
use Xvlvv\DTO\WorkerProfileDTO;
use Xvlvv\Entity\User;
use Xvlvv\Enums\UserRole;
use Xvlvv\Exception\UserWithEmailAlreadyExistsException;
use Xvlvv\Repository\CityRepositoryInterface;
use Xvlvv\Repository\UserRepositoryInterface;
use Yii;
use yii\base\Exception;
use yii\web\NotFoundHttpException;

/**
 * Сервис для аутентификации и регистрации пользователей
 */
final readonly class AuthService
{
    /**
     * @param UserRepositoryInterface $userRepository
     * @param CityRepositoryInterface $cityRepository
     */
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private CityRepositoryInterface $cityRepository,
    ) {
    }

    /**
     * Проверяет учетные данные пользователя
     *
     * @param string $email
     * @param string $password
     * @return User|null
     */
    public function authenticate(string $email, string $password): ?User
    {
        $user = $this->userRepository->getByEmail($email);

        if ($user === null || !$user->isValidPassword($password)) {
            return null;
        }

        return $user;
    }

    /**
     * Регистрирует нового пользователя в системе
     *
     * @param RegisterUserDTO $dto DTO с данными для регистрации
     * @return int ID нового пользователя
     * @throws UserWithEmailAlreadyExistsException если email уже занят
     * @throws NotFoundHttpException|Exception если город не найден
     */
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
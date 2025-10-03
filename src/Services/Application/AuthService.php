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
     * @param UserRepositoryInterface $userRepository Репозиторий для работы с пользователями
     * @param CityRepositoryInterface $cityRepository Репозиторий для работы с городами
     */
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private CityRepositoryInterface $cityRepository,
    ) {
    }

    /**
     * Проверяет учетные данные пользователя по email и паролю.
     *
     * @param string $email Email пользователя.
     * @param string $password Пароль пользователя в открытом виде.
     * @return User|null Возвращает сущность User в случае успеха, иначе null.
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
     * Аутентифицирует пользователя, ранее зарегистрированного через VK.
     * Проверяет, что email и VK ID совпадают с сохраненными данными.
     *
     * @param string $email Email пользователя.
     * @param int|null $vkId ID пользователя ВКонтакте.
     * @return User|null Возвращает сущность User в случае успеха, иначе null.
     */
    public function authenticateWithVkId(string $email, ?int $vkId): ?User
    {
        $user = $this->userRepository->getByEmail($email);

        if ($user === null || $user->getVkId() !== $vkId) {
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
                showContactsOnlyToCustomers: true,
            ),
        };

        $profile = $profileFactory->createFromDTO($profileDTO);

        $passwordHash = null;

        if (null === $dto->vkId) {
            $passwordHash = Yii::$app->getSecurity()->generatePasswordHash($dto->password);
        }

        $authKey = Yii::$app->getSecurity()->generateRandomString();

        $user = new User(
            $dto->name,
            $dto->email,
            $passwordHash,
            $userRole,
            $profile,
            $city,
            $authKey,
            avatarPath: $dto->avatar,
            vkId: $dto->vkId,
        );

        $user = $this->userRepository->save($user);

        return $user->getId();
    }
}
<?php

declare(strict_types=1);

namespace Xvlvv\Services\Application;

use Xvlvv\DTO\ChangePasswordDTO;
use Xvlvv\Repository\UserRepositoryInterface;
use Yii;
use yii\base\Exception;
use yii\web\NotFoundHttpException;

/**
 * Сервис для управления настройками безопасности пользователя
 */
readonly final class SecurityService
{
    /**
     * @param UserRepositoryInterface $userRepository Репозиторий для работы с пользователями
     */
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * Изменяет пароль пользователя
     *
     * @param int $userId ID пользователя
     * @param ChangePasswordDTO $dto DTO с новым паролем
     * @return void
     * @throws NotFoundHttpException|Exception если пользователь не найден
     */
    public function changePassword(int $userId, ChangePasswordDTO $dto): void
    {
        $user = $this->userRepository->getByIdOrFail($userId);
        $newPasswordHash = Yii::$app->security->generatePasswordHash($dto->newPassword);
        $user->changePassword($newPasswordHash);

        $this->userRepository->save($user);
    }
}
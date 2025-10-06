<?php

namespace Xvlvv\Services\Application;

use app\auth\VkIdOauth;
use Exception;
use Xvlvv\Entity\User;
use Xvlvv\Repository\UserRepositoryInterface;
use Yii;
use yii\web\BadRequestHttpException;

/**
 * Сервис для обработки аутентификации через VK ID
 */
readonly final class VkAuthService
{
    /**
     * @param UserRepositoryInterface $userRepo Репозиторий для работы с пользователями
     * @param VkIdOauth $client OAuth-клиент для взаимодействия с VK ID
     */
    public function __construct(
        private UserRepositoryInterface $userRepo,
        private VkIdOauth $client
    ) {
    }

    /**
     * Обрабатывает авторизацию пользователя через VK.
     *
     * @param string $code
     * @param string $deviceId
     * @return User|null Возвращает объект User, если удалось авторизовать/привязать аккаунт.
     *                   Возвращает null, если требуется регистрация.
     * @throws BadRequestHttpException
     */
    public function authenticate(string $code, string $deviceId): ?User
    {
        $this->client->saveDeviceId($deviceId);

        try {
            $userData = $this->client
                ->fetchAccessToken($code)
                ->getUserData();
        } catch (Exception $e) {
            throw new BadRequestHttpException();
        }

        if (empty($userData)) {
            throw new BadRequestHttpException();
        }

        $vkId = $userData['user_id'] ?? '';

        if (empty($vkId)) {
            throw new BadRequestHttpException();
        }

        $user = $this->userRepo->getByVkId((int)$vkId);

        if ($user) {
            return $user;
        }

        $user = $this->userRepo->getByEmail($userData['email'] ?? null);

        if ($user) {
            $user->updateWithVkId($vkId);
            $this->userRepo->update($user);

            return $user;
        }

        $firstName = $userData['first_name'] ?? '';
        $lastName = $userData['last_name'] ?? '';

        if (empty($firstName) || empty($lastName)) {
            throw new BadRequestHttpException();
        }

        Yii::$app->session->set('vk_user_data', [
            'name' => $userData['first_name'] . ' ' . $userData['last_name'],
            'email' => $userData['email'],
            'vk_id' => $vkId,
            'avatar' => $userData['avatar'] ?? null,
        ]);

        return null;
    }
}
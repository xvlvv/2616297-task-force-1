<?php

namespace app\models;

use Xvlvv\Repository\UserRepositoryInterface;
use Yii;
use yii\web\IdentityInterface;
use Xvlvv\Entity\User;
use app\models\User as UserModel;

/**
 * Класс Identity для аутентификации пользователя в Yii.
 * Обертка над доменной сущностью User.
 */
readonly final class UserIdentity implements IdentityInterface
{
    /**
     * @param User $user Доменная сущность пользователя
     */
    public function __construct(
        private User $user
    ) {
    }

    /**
     * {@inheritdoc}
     * Находит Identity по ID пользователя, используя репозиторий.
     */
    public static function findIdentity($id): ?IdentityInterface
    {
        /** @var UserRepositoryInterface $userRepo */
        $userRepo = Yii::$container->get(UserRepositoryInterface::class);
        $user = $userRepo->getById($id);
        return $user ? new self($user) : null;
    }

    /**
     * Возвращает вложенную доменную сущность User.
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     * Находит Identity по токену доступа.
     */
    public static function findIdentityByAccessToken($token, $type = null): ?string
    {
        return UserModel::find()->where(['access_token' => $token])->one() ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): int|string
    {
        return $this->getUser()->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey(): ?string
    {
        return $this->getUser()->getAccessToken();
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->getUser()->getAccessToken() === $authKey;
    }
}
<?php

namespace app\models;

use Xvlvv\Repository\UserRepositoryInterface;
use Yii;
use yii\web\IdentityInterface;
use Xvlvv\Entity\User;
use app\models\User as UserModel;

final class UserIdentity implements IdentityInterface
{
    public function __construct(
        private User $user
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function findIdentity($id): ?IdentityInterface
    {
        $userRepo = Yii::$container->get(UserRepositoryInterface::class);
        $user = $userRepo->getById($id);
        return $user ? new self($user) : null;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @inheritDoc
     */
    public static function findIdentityByAccessToken($token, $type = null): ?string
    {
        return UserModel::find()->where(['access_token' => $token])->one() ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getId(): int|string
    {
        return $this->getUser()->getId();
    }

    /**
     * @inheritDoc
     */
    public function getAuthKey()
    {
        return $this->getUser()->getAccessToken();
    }

    /**
     * @inheritDoc
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->getUser()->getAccessToken() === $authKey;
    }
}
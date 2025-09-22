<?php

namespace app\models;

use Xvlvv\Repository\UserRepositoryInterface;
use Yii;
use yii\web\IdentityInterface;
use \Xvlvv\Entity\User;

class UserIdentity implements IdentityInterface
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
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * @inheritDoc
     */
    public function getId(): int|string
    {
        return $this->user->getId();
    }

    /**
     * @inheritDoc
     */
    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
    }

    /**
     * @inheritDoc
     */
    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
    }
}
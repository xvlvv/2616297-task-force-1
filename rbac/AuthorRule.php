<?php

namespace app\rbac;

use Yii;
use yii\base\InvalidConfigException;
use yii\rbac\Rule;

class AuthorRule extends Rule
{
    public $name = 'isAuthor';
    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    public function execute($user, $item, $params): bool
    {
        $userRepo = Yii::$app->get('UserRepositoryInterface');
        return $userRepo->isAuthor((int) $user);
    }
}
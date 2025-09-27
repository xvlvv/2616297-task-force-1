<?php

namespace app\rbac;

use Yii;
use yii\base\InvalidConfigException;
use yii\rbac\Rule;

class TaskAuthorRule extends Rule
{
    public $name = 'isTaskAuthor';
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
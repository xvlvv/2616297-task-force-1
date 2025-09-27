<?php

use app\rbac\AuthorRule;
use Xvlvv\Enums\UserRole;
use yii\base\InvalidConfigException;
use yii\db\Migration;
use yii\rbac\DbManager;

class m250922_184426_init_rbac extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        if (!$auth instanceof DbManager) {
            throw new InvalidConfigException(
                'You should configure "authManager" component to use database before executing this migration.'
            );
        }

        $worker = $auth->createRole(UserRole::WORKER->value);
        $customer = $auth->createRole(UserRole::CUSTOMER->value);

        $auth->add($worker);
        $auth->add($customer);

        $publishTask = $auth->createPermission('publishTask');
        $auth->add($publishTask);
        $auth->addChild($customer, $publishTask);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        if (!$auth instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }

        $auth->removeAll();
    }
}

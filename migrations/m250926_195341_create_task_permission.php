<?php

use app\rbac\AuthorRule;
use app\rbac\TaskAuthorRule;
use app\rbac\TaskWorkerRule;
use Xvlvv\Enums\UserRole;
use yii\base\InvalidConfigException;
use yii\db\Migration;
use yii\rbac\DbManager;

class m250926_195341_create_task_permission extends Migration
{
    public function getAuthManager(): DbManager
    {
        $auth = Yii::$app->authManager;

        if (!$auth instanceof DbManager) {
            throw new InvalidConfigException(
                'You should configure "authManager" component to use database before executing this migration.'
            );
        }

        return $auth;
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $auth = $this->getAuthManager();

        $workerRole = $auth->getRole(UserRole::WORKER->value);

        if (null === $workerRole) {
            throw new RuntimeException('User role not found');
        }

        $customerRole = $auth->getRole(UserRole::CUSTOMER->value);

        if (null === $customerRole) {
            throw new RuntimeException('User role not found');
        }

        $taskAuthorRule = new TaskAuthorRule();
        $auth->add($taskAuthorRule);

        $manageTaskResponses = $auth->createPermission('manageTaskResponses');
        $manageTaskResponses->ruleName = $taskAuthorRule->name;
        $auth->add($manageTaskResponses);
        $auth->addChild($customerRole, $manageTaskResponses);

        $taskWorkerRule = new TaskWorkerRule();
        $auth->add($taskWorkerRule);

        $setTaskFailStatus = $auth->createPermission('failTask');
        $setTaskFailStatus->ruleName = $taskWorkerRule->name;
        $auth->add($setTaskFailStatus);
        $auth->addChild($workerRole, $setTaskFailStatus);

        $applyToTask = $auth->createPermission('applyToTask');
        $auth->add($applyToTask);

        $auth->addChild($workerRole, $applyToTask);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $auth = $this->getAuthManager();
        $applyToTask = $auth->getPermission('applyToTask');

        if (null === $applyToTask) {
            throw new RuntimeException('Permission not found');
        }

        $manageTaskResponses = $auth->getPermission('manageTaskResponses');

        if (null === $manageTaskResponses) {
            throw new RuntimeException('Permission not found');
        }

        $workerRole = $auth->getRole(UserRole::WORKER->value);

        if (null === $workerRole) {
            throw new RuntimeException('User role not found');
        }

        $customerRole = $auth->getRole(UserRole::CUSTOMER->value);

        if (null === $customerRole) {
            throw new RuntimeException('User role not found');
        }

        $auth->removeChild($workerRole, $applyToTask);
        $auth->remove($applyToTask);

        $manageTaskResponses = $auth->getPermission('manageTaskResponses');

        if (null === $manageTaskResponses) {
            throw new RuntimeException('Permission not found');
        }

        $auth->remove($manageTaskResponses);

        $taskAuthorRule = $auth->getRule('isTaskAuthor');

        if (null === $taskAuthorRule) {
            throw new RuntimeException('Rule not found');
        }

        $auth->remove($taskAuthorRule);

        $setTaskFailStatus = $auth->getPermission('failTask');

        if (null === $setTaskFailStatus) {
            throw new RuntimeException('Permission not found');
        }

        $auth->remove($setTaskFailStatus);

        $taskWorkerRule = $auth->getRule('isTaskWorker');

        if (null === $taskWorkerRule) {
            throw new RuntimeException('Rule not found');
        }

        $auth->remove($taskWorkerRule);
    }
}

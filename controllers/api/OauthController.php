<?php

namespace app\controllers\api;

use app\models\UserIdentity;
use Xvlvv\Services\Application\VkAuthService;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class OauthController extends Controller
{
    public function beforeAction($action): bool
    {
        if (!Yii::$app->authClientCollection->hasClient('vk-id')) {
            throw new NotFoundHttpException('VK auth feature is not enabled.');
        }
        return parent::beforeAction($action);
    }

    public function actionRedirect(): Response
    {
        $client = Yii::$app->authClientCollection->getClient('vk-id');
        return $this->redirect($client->buildAuthUrl());
    }

    public function actionCallback(VkAuthService $authService): Response
    {
        $code = Yii::$app->request->get('code');
        $deviceId = Yii::$app->request->get('device_id');

        if (!$code || !$deviceId) {
            throw new BadRequestHttpException();
        }

        $user = $authService->authenticate($code, $deviceId);

        if ($user) {
            $identity = new UserIdentity($user);
            Yii::$app->user->login($identity);
            return $this->redirect('/tasks');
        }

        return $this->redirect(['site/register']);
    }
}
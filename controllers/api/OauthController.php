<?php

namespace app\controllers\api;

use app\models\UserIdentity;
use Xvlvv\Services\Application\VkAuthService;
use Yii;
use yii\base\Action;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Контроллер для обработки OAuth 2.1 аутентификации через VK ID.
 */
class OauthController extends Controller
{

    /**
     * Проверяет, включена ли функция аутентификации через VK перед выполнением любого действия.
     * @param Action $action
     * @return bool
     * @throws NotFoundHttpException если клиент 'vk-id' не настроен.
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        if (!Yii::$app->authClientCollection->hasClient('vk-id')) {
            throw new NotFoundHttpException('VK auth feature is not enabled.');
        }
        return parent::beforeAction($action);
    }

    /**
     * Перенаправляет пользователя на страницу авторизации VK.
     * @return Response
     */
    public function actionRedirect(): Response
    {
        $client = Yii::$app->authClientCollection->getClient('vk-id');
        return $this->redirect($client->buildAuthUrl());
    }

    /**
     * Обрабатывает обратный вызов (callback) от сервера VK после авторизации пользователя.
     * @param VkAuthService $authService Сервис для обработки логики аутентификации.
     * @return Response
     * @throws BadRequestHttpException если отсутствуют обязательные параметры `code` или `device_id`.
     */
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
<?php

namespace app\controllers;

use app\models\City;
use app\models\RegistrationForm;
use app\models\UserIdentity;
use RuntimeException;
use Xvlvv\DTO\RegisterUserDTO;
use Xvlvv\Exception\UserWithEmailAlreadyExistsException;
use Xvlvv\Services\Application\AuthService;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use yii\widgets\ActiveForm;

/**
 * Контроллер для обработки основных страниц сайта, таких как главная и регистрация.
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['register', 'login', 'logout'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['register', 'login'],
                        'roles' => ['?']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['logout'],
                        'roles' => ['@']
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'apply' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @param AuthService $authService
     * @return Response|string|array
     */
    public function actionIndex(AuthService $authService): Response|string|array
    {
        if (!Yii::$app->user->isGuest) {
            $this->layout = 'main';
            return $this->redirect('/tasks');
        }

        $this->layout = 'index';
        $loginForm = new LoginForm($authService);

        if (Yii::$app->request->isAjax && $loginForm->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($loginForm);
        }

        if ($loginForm->load(Yii::$app->request->post()) && $loginForm->validate()) {
            $user = $loginForm->getUser();
            $identity = new UserIdentity($user);
            Yii::$app->user->login($identity);

            return $this->refresh();
        }

        return $this->render('index', compact('loginForm'));
    }

    /**
     * Обрабатывает регистрацию нового пользователя.
     * При GET-запросе отображает форму, при POST - обрабатывает данные.
     *
     * @param AuthService $authService Сервис для регистрации пользователей (внедряется DI-контейнером).
     * @return string|Response Рендер страницы или редирект на главную в случае успеха.
     * @throws UserWithEmailAlreadyExistsException Если пользователь с таким email уже существует.
     * @throws NotFoundHttpException|Exception Если указанный город не найден.
     */
    public function actionRegister(AuthService $authService): string|Response
    {
        $formModel = new RegistrationForm();

        $cities = ArrayHelper::map(
            City::find()->select(['id', 'name'])->asArray()->all(),
            'id',
            'name'
        );

        $vkUserData = Yii::$app->session->get('vk_user_data', false);
        $isRegisterWithVK = (bool) $vkUserData;

        $formModel->scenario = $isRegisterWithVK ?
            RegistrationForm::SCENARIO_VK_REGISTER
            : RegistrationForm::SCENARIO_DEFAULT_REGISTER;

        if ($isRegisterWithVK) {
            $formModel->load($vkUserData, '');
        }

        $formModel->load(Yii::$app->request->post());

        if (!Yii::$app->request->isPost || !$formModel->validate()) {
            return $this->render(
                'register',
                compact('formModel', 'cities', 'isRegisterWithVK')
            );
        }

        $registerDTO = new RegisterUserDTO(
            $formModel->name,
            $formModel->email,
            $formModel->cityId,
            $formModel->isWorker,
            $formModel->password,
            $isRegisterWithVK ? (int) $vkUserData['vk_id'] : null,
            $isRegisterWithVK ? $vkUserData['avatar'] ?? null : null,
        );

        $authService->register($registerDTO);

        if ($isRegisterWithVK) {
            $user = $authService->authenticateWithVkId($formModel->email, $vkUserData['vk_id'] ?? null);
            Yii::$app->session->remove('vk_user_data');
        } else {
            $user = $authService->authenticate($formModel->name, $formModel->password);
        }

        if (null === $user) {
            throw new RuntimeException('Ошибка при регистрации');
        }

        $identity = new UserIdentity($user);
        Yii::$app->user->login($identity);
        return $this->redirect('/tasks');
    }

    /**
     * Обрабатывает выход из системы
     *
     * @return Response
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}

<?php

use Xvlvv\DataMapper\CityMapper;
use Xvlvv\Services\Application\GeocoderInterface;
use Xvlvv\Services\Application\LocationService;
use Xvlvv\Services\Application\YandexGeocoderService;
use yii\httpclient\Client;
use Xvlvv\DataMapper\TaskMapper;
use Xvlvv\DataMapper\UserMapper;
use Xvlvv\Repository\CategoryRepository;
use Xvlvv\Repository\CategoryRepositoryInterface;
use Xvlvv\Repository\CityRepository;
use Xvlvv\Repository\CityRepositoryInterface;
use Xvlvv\Repository\ReviewRepository;
use Xvlvv\Repository\ReviewRepositoryInterface;
use Xvlvv\Repository\TaskRepository;
use Xvlvv\Repository\TaskRepositoryInterface;
use Xvlvv\Repository\TaskResponseRepository;
use Xvlvv\Repository\TaskResponseRepositoryInterface;
use Xvlvv\Repository\UserRepository;
use Xvlvv\Repository\UserRepositoryInterface;
use Xvlvv\Services\Application\AuthService;
use Xvlvv\Services\Application\CancelTaskService;
use Xvlvv\Services\Application\FailTaskService;
use Xvlvv\Services\Application\FinishTaskService;
use Xvlvv\Services\Application\PublishTaskService;
use Xvlvv\Services\Application\StartTaskService;
use Xvlvv\Services\Application\TaskResponseService;

/**
 * @var array $params
 * @var array $db
 */
$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'container' => [
        'definitions' => [
            TaskRepositoryInterface::class => function () {
                $taskResponseRepo = Yii::$container->get(TaskResponseRepositoryInterface::class);
                $mapper = Yii::$container->get(TaskMapper::class);
                return new TaskRepository($taskResponseRepo, $mapper);
            },
            CityRepositoryInterface::class => function () {
                $mapper = Yii::$container->get(CityMapper::class);
                return new CityRepository($mapper);
            },
            CityMapper::class => CityMapper::class,
            CategoryRepositoryInterface::class => CategoryRepository::class,
            TaskResponseRepositoryInterface::class => function () {
                $userMapper = Yii::$container->get(UserMapper::class);
                $reviewRepo = Yii::$container->get(ReviewRepositoryInterface::class);
                $taskMapper = Yii::$container->get(TaskMapper::class);

                return new TaskResponseRepository($reviewRepo, $userMapper, $taskMapper);
            },
            ReviewRepositoryInterface::class => ReviewRepository::class,
            UserMapper::class => UserMapper::class,
            UserRepositoryInterface::class => function () {
                $reviewRepo = Yii::$container->get(ReviewRepositoryInterface::class);
                $taskRepo = Yii::$container->get(TaskRepositoryInterface::class);
                $userMapper = Yii::$container->get(UserMapper::class);
                return new UserRepository($reviewRepo, $taskRepo, $userMapper);
            },
            TaskMapper::class => TaskMapper::class,
            AuthService::class => AuthService::class,
            StartTaskService::class => StartTaskService::class,
            PublishTaskService::class  => PublishTaskService::class,
            TaskResponseService::class => TaskResponseService::class,
            FinishTaskService::class => FinishTaskService::class,
            CancelTaskService::class => CancelTaskService::class,
            FailTaskService::class => FailTaskService::class,
            Client::class => Client::class,
            LocationService::class => function () {
                $geocoder = Yii::$container->get(GeocoderInterface::class);
                return new LocationService($geocoder);
            },
            GeocoderInterface::class => function () {
                $client = Yii::$container->get(Client::class);
                $cityRepo = Yii::$container->get(CityRepositoryInterface::class);
                return new YandexGeocoderService($client, $cityRepo);
            },
        ],
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'KrAle6Af_a3Xn-2c39xxE8Bl3hS0q9aS',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\UserIdentity',
            'enableAutoLogin' => true,
            'loginUrl' => ['site/index'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'db' => $db,
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'locale' => 'ru-RU',
            'defaultTimeZone' => 'Europe/Moscow',
            'dateFormat' => 'php:d.m.Y',
            'datetimeFormat' => 'php:d.m.Y H:i:s',
            'timeFormat' => 'php:H:i:s',
        ],
        'taskStateManager' => [
            'class' => 'Xvlvv\Services\TaskStateManager',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',
                'tasks' => 'task/index',
                'tasks/view/<id:\d+>' => 'task/view',
                'task/apply/<id:\d+>' => 'task/apply',
                'user/view/<id:\d+>' => 'user/view',
                'register' => 'site/register',
                'logout' => 'site/logout',
                'publish' => 'task/publish',
                'tasks/apply' => 'task/apply',
                'task/rejectResponse/<id:\d+>' => 'task/reject-response',
                'task/start/<id:\d+>' => 'task/start',
                'task/complete/<id:\d+>' => 'task/complete',
                'task/fail/<id:\d+>' => 'task/fail',
                'GET api/locations' => 'api/location/search',
            ],
        ],
        'session' => [
            'class' => 'yii\web\Session',
            'cookieParams' => ['lifetime' => 7 * 24 *60 * 60]
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => explode(',', $_ENV['DEV_ALLOWED_IPS'] ?? '127.0.0.1, ::1'),
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => explode(',', $_ENV['DEV_ALLOWED_IPS'] ?? '127.0.0.1, ::1'),
    ];
}

return $config;

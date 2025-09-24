<?php

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
use Xvlvv\Services\Application\PublishTaskService;

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
                return new TaskRepository($taskResponseRepo);
            },
            CityRepositoryInterface::class => CityRepository::class,
            CategoryRepositoryInterface::class => CategoryRepository::class,
            TaskResponseRepositoryInterface::class => function () {
                $reviewRepo = Yii::$container->get(ReviewRepositoryInterface::class);
                return new TaskResponseRepository($reviewRepo);
            },
            ReviewRepositoryInterface::class => ReviewRepository::class,
            UserMapper::class => UserMapper::class,
            UserRepositoryInterface::class => function () {
                $reviewRepo = Yii::$container->get(ReviewRepositoryInterface::class);
                $taskRepo = Yii::$container->get(TaskRepositoryInterface::class);
                $userMapper = Yii::$container->get(UserMapper::class);
                return new UserRepository($reviewRepo, $taskRepo, $userMapper);
            },
            AuthService::class => AuthService::class,
            PublishTaskService::class  => PublishTaskService::class,

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

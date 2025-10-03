<?php

declare(strict_types=1);

namespace app\controllers;

use Xvlvv\Repository\UserRepositoryInterface;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Контроллер для отображения профилей пользователей.
 */
class UserController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['@']
                    ],
                ],
            ]
        ];
    }

    /**
     * Отображает публичный профиль исполнителя.
     *
     * @param int $id ID пользователя (исполнителя).
     * @param UserRepositoryInterface $userRepository Репозиторий для получения данных о пользователе.
     * @return string Рендер страницы профиля.
     */
    public function actionView(int $id, UserRepositoryInterface $userRepository): string
    {
        $user = $userRepository->getWorkerForView($id);

        return $this->render('view', compact('user'));
    }
}
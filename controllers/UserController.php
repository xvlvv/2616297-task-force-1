<?php

declare(strict_types=1);

namespace app\controllers;

use Xvlvv\Repository\UserRepositoryInterface;
use yii\web\Controller;

class UserController extends Controller
{
    public function actionView(int $id, UserRepositoryInterface $userRepository): string
    {
        $user = $userRepository->getWorkerForView($id);

        return $this->render('view', compact('user'));
    }
}
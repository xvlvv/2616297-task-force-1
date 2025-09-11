<?php

namespace app\controllers;

use Xvlvv\Repository\TaskRepositoryInterface;
use yii\data\ArrayDataProvider;
use yii\web\Controller;

class TasksController extends Controller
{
    public function actionIndex(TaskRepositoryInterface $taskRepository)
    {
        $data = $taskRepository->getNewTasks(0, 3);
        $provider = new ArrayDataProvider([
            'allModels' => $data,
        ]);
        return $this->render('index', ['tasks' => $provider]);
    }
}
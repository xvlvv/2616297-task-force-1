<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\TaskSearch;
use Xvlvv\DTO\GetNewTasksDTO;
use Xvlvv\Repository\TaskRepositoryInterface;
use Yii;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class TasksController extends Controller
{
    public function actionIndex(TaskRepositoryInterface $taskRepository): string
    {
        $pageSize = 1;
        $model = new TaskSearch();

        $model->load(Yii::$app->request->get());

        $count = $taskRepository->getFilteredTasksQueryCount(
            new GetNewTasksDTO(
                0,
                0,
                $model->categories,
                $model->checkWorker,
                $model->createdAt
            )
        );

        $pagination = new Pagination([
            'totalCount' => $count,
            'defaultPageSize' => $pageSize,
            'route' => '/tasks',
        ]);

        $getNewTasksDTO = new GetNewTasksDTO(
            $pagination->getOffset(),
            $pageSize,
            $model->categories,
            $model->checkWorker,
            $model->createdAt
        );

        $dto = $taskRepository->getNewTasks($getNewTasksDTO);

        $provider = new ArrayDataProvider([
            'allModels' => $dto->tasks,
        ]);

        $categories = ArrayHelper::map($dto->categories, 'id', 'name');

        return $this->render(
            'index',
            [
                'tasks' => $provider,
                'model' => $model,
                'categories' => $categories,
                'pagination' => $pagination,
            ]
        );
    }

    public function actionView(): string
    {
        return '1';
    }
}
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

/**
 * Контроллер для управления заданиями (просмотр списка и детальной страницы).
 */
class TaskController extends Controller
{
    /**
     * Отображает страницу со списком новых заданий с фильтрацией и пагинацией.
     *
     * @param TaskRepositoryInterface $taskRepository Репозиторий для получения данных о заданиях.
     * @return string Рендер страницы списка заданий.
     */
    public function actionIndex(TaskRepositoryInterface $taskRepository): string
    {
        $pageSize = 1;
        $model = new TaskSearch();

        $model->load(Yii::$app->request->get());

        $count = $taskRepository->getFilteredTasksQueryCount(
            new GetNewTasksDTO(
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
            $model->categories,
            $model->checkWorker,
            $model->createdAt,
            $pagination->getOffset(),
            $pageSize,
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

    /**
     * Отображает детальную страницу одного задания.
     *
     * @param int $id ID задания.
     * @param TaskRepositoryInterface $taskRepository Репозиторий для получения данных о задании.
     * @return string Рендер страницы задания.
     * @throws \yii\web\NotFoundHttpException Если задание с указанным ID не найдено.
     */
    public function actionView(int $id, TaskRepositoryInterface $taskRepository): string
    {
        $task = $taskRepository->getTaskForView($id);

        return $this->render(
            'view',
            [
                'task' => $task,
            ]
        );
    }
}
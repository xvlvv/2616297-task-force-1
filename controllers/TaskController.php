<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\PublishForm;
use app\models\TaskSearch;
use DateTimeImmutable;
use RuntimeException;
use Xvlvv\DTO\CreateTaskDTO;
use Xvlvv\DTO\GetNewTasksDTO;
use Xvlvv\DTO\SaveTaskDTO;
use Xvlvv\Repository\CategoryRepositoryInterface;
use Xvlvv\Repository\TaskRepositoryInterface;
use Xvlvv\Services\Application\PublishTaskService;
use Yii;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

/**
 * Контроллер для управления заданиями (просмотр списка и детальной страницы).
 */
class TaskController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['publish'],
                        'matchCallback' => fn () => Yii::$app->user->can('publishTask')
                    ]
                ],
            ]
        ];
    }

    /**
     * Отображает страницу со списком новых заданий с фильтрацией и пагинацией.
     *
     * @param TaskRepositoryInterface $taskRepository Репозиторий для получения данных о заданиях.
     * @return string Рендер страницы списка заданий.
     */
    public function actionIndex(TaskRepositoryInterface $taskRepository): string
    {
        $pageSize = 3;
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
     */
    public function actionView(int $id, TaskRepositoryInterface $taskRepository): string
    {
        $userId = Yii::$app->user->identity->getUser()->getId();
        $task = $taskRepository->getTaskForView($id, $userId);

        return $this->render(
            'view',
            [
                'task' => $task,
            ]
        );
    }

    public function actionPublish(
        PublishTaskService $publishTaskService,
        CategoryRepositoryInterface $categoryRepo,
    ): string|Response {
        $formModel = new PublishForm();
        $categories = ArrayHelper::map(
            $categoryRepo->getAll(),
            'id',
            'name'
        );

        if (!Yii::$app->request->isPost) {
            return $this->render('publish', compact('formModel', 'categories'));
        }

        $formModel->load(Yii::$app->request->post());

        if (!$formModel->validate()) {
            return $this->render('publish', compact('formModel', 'categories'));
        }

        $files = $formModel->upload();

        if (!is_array($files)) {
            return $this->render('publish', compact('formModel', 'categories'));
        }

        $saveTaskDTO = new CreateTaskDTO(
            $formModel->name,
            $formModel->description,
            $formModel->categoryId,
            Yii::$app->user->identity->getUser()->getId(),
            DateTimeImmutable::createFromFormat('Y-m-d', $formModel->endDate),
            budget: $formModel->budget,
            files: $files,
        );

        $id = $publishTaskService->publish($saveTaskDTO);

        if (null === $id) {
            throw new RuntimeException('Не удалось создать задачу');
        }

        $newTask = Url::to(['/task/view', 'id' => $id]);
        return $this->redirect($newTask);
    }

    public function actionApply(int $id): string
    {
        return '1';
    }
}
<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\ApplyForm;
use app\models\PublishForm;
use app\models\CompleteForm;
use app\models\TaskSearch;
use DateTimeImmutable;
use RuntimeException;
use Xvlvv\DTO\CancelTaskDTO;
use Xvlvv\DTO\CreateTaskDTO;
use Xvlvv\DTO\FailTaskDTO;
use Xvlvv\DTO\GetNewTasksDTO;
use Xvlvv\DTO\SaveReviewDTO;
use Xvlvv\DTO\SaveTaskResponseDTO;
use Xvlvv\DTO\StartTaskDTO;
use Xvlvv\Repository\CategoryRepositoryInterface;
use Xvlvv\Repository\TaskRepositoryInterface;
use Xvlvv\Repository\TaskResponseRepositoryInterface;
use Xvlvv\Services\Application\CancelTaskService;
use Xvlvv\Services\Application\FailTaskService;
use Xvlvv\Services\Application\FinishTaskService;
use Xvlvv\Services\Application\PublishTaskService;
use Xvlvv\Services\Application\StartTaskService;
use Xvlvv\Services\Application\TaskResponseService;
use Yii;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;

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
                    ],
                    [
                        'allow' => true,
                        'actions' => ['apply'],
                        'matchCallback' => fn () => Yii::$app->user->can('applyToTask')
                    ],
                    [
                        'allow' => true,
                        'actions' => ['reject-response', 'start'],
                        'matchCallback' => function () {
                            $responseId = Yii::$app->request->get('id');

                            if (!$responseId) {
                                return false;
                            }

                            $repo = Yii::$container->get(TaskResponseRepositoryInterface::class);
                            $taskId = $repo->getTaskIdByResponseId((int)$responseId);

                            return Yii::$app->user->can('manageTaskResponses', ['taskId' => $taskId]);
                        }
                    ],
                    [
                        'allow' => true,
                        'actions' => ['complete', 'cancel'],
                        'matchCallback' => function () {
                            $taskId = Yii::$app->request->get('id');

                            if (!$taskId) {
                                return false;
                            }

                            return Yii::$app->user->can('manageTaskResponses', ['taskId' => $taskId]);
                        }
                    ],
                    [
                        'allow' => true,
                        'actions' => ['fail'],
                        'matchCallback' => function () {
                            $taskId = Yii::$app->request->get('id');

                            if (!$taskId) {
                                return false;
                            }

                            return Yii::$app->user->can('failTask', ['taskId' => $taskId]);
                        }
                    ],
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
        $applyForm = new ApplyForm();
        $completeForm = new CompleteForm();

        return $this->render(
            'view',
            compact('task', 'applyForm', 'completeForm')
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

    public function actionApply(int $id, TaskResponseService $responseService): array|Response
    {
        $completeForm = new ApplyForm();
        $completeForm->load(Yii::$app->request->post());

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($completeForm);
        }

        if (!$completeForm->validate()) {
            throw new BadRequestHttpException('Ошибка при сохранении отклика');
        }

        $saveResponseDTO = new SaveTaskResponseDTO(
            $id,
            Yii::$app->user->identity->getUser()->getId(),
            $completeForm->description,
            (int) $completeForm->price,
        );

        $responseService->createResponse($saveResponseDTO);

        return $this->redirect(['task/view', 'id' => $id]);
    }

    public function actionRejectResponse(
        int $id,
        TaskResponseService $responseService,
        TaskResponseRepositoryInterface $taskResponseRepository
    ): Response {
        $taskId = $taskResponseRepository->getTaskIdByResponseId($id);
        $responseService->rejectResponse($id);

        return $this->redirect(['task/view', 'id' => $taskId]);
    }

    public function actionStart(
        int $id,
        StartTaskService $service,
        TaskResponseRepositoryInterface $taskResponseRepository
    ): Response {
        $responseEntity = $taskResponseRepository->getByIdOrFail($id);
        $taskId = $responseEntity->getTaskId();

        $startTaskDTO = new StartTaskDTO(
            $taskId,
            Yii::$app->user->identity->getUser()->getId(),
            $responseEntity->getWorkerId(),
        );

        $service->handle($startTaskDTO);

        return $this->redirect(['task/view', 'id' => $taskId]);
    }

    public function actionComplete(
        int $id,
        FinishTaskService $service,
    ): array|Response {
        $completeForm = new CompleteForm();
        $completeForm->load(Yii::$app->request->post());

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($completeForm);
        }

        if (!$completeForm->validate()) {
            throw new BadRequestHttpException('Ошибка при сохранении отклика');
        }

        $saveReviewDTO = new SaveReviewDTO(
            (int) $completeForm->rating,
            $completeForm->comment,
            $id,
            Yii::$app->user->identity->getUser()->getId(),
        );

        $service->handle($saveReviewDTO);

        return $this->redirect(['task/view', 'id' => $id]);
    }

    public function actionCancel(
        int $id,
        CancelTaskService $service,
    ): array|Response {
        $cancelTaskDTO = new CancelTaskDTO(
            $id,
            Yii::$app->user->identity->getUser()->getId(),
        );

        $service->handle($cancelTaskDTO);

        return $this->redirect(['task/view', 'id' => $id]);
    }

    public function actionFail(
        int $id,
        FailTaskService $service,
    ): array|Response {
        $failTaskDTO = new FailTaskDTO(
            Yii::$app->user->identity->getUser()->getId(),
            $id,
        );

        $service->handle($failTaskDTO);

        return $this->redirect(['task/view', 'id' => $id]);
    }
}
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
use Xvlvv\Entity\Category;
use Xvlvv\Repository\CategoryRepositoryInterface;
use Xvlvv\Repository\TaskRepositoryInterface;
use Xvlvv\Repository\TaskResponseRepositoryInterface;
use Xvlvv\Services\Application\CancelTaskService;
use Xvlvv\Services\Application\FailTaskService;
use Xvlvv\Services\Application\FinishTaskService;
use Xvlvv\Services\Application\MyTasksService;
use Xvlvv\Services\Application\PublishTaskService;
use Xvlvv\Services\Application\StartTaskService;
use Xvlvv\Services\Application\TaskResponseService;
use Yii;
use yii\base\Exception;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Контроллер для управления заданиями (просмотр списка и детальной страницы).
 */
class TaskController extends Controller
{
    /**
     * {@inheritDoc}
     */
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
                        'matchCallback' => fn() => Yii::$app->user->can('publishTask')
                    ],
                    [
                        'allow' => true,
                        'actions' => ['apply', 'my'],
                        'matchCallback' => fn() => Yii::$app->user->can('applyToTask')
                    ],
                    [
                        'allow' => true,
                        'actions' => ['reject-response', 'start'],
                        'matchCallback' => function () {
                            $responseId = Yii::$app->request->get('id');

                            if (!$responseId) {
                                return false;
                            }

                            /** @var TaskResponseRepositoryInterface $repo */
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
        $user = Yii::$app->user?->identity?->getUser();

        $model->load(Yii::$app->request->get());

        $count = $taskRepository->getFilteredTasksQueryCount(
            new GetNewTasksDTO(
                $model->categories,
                $model->checkWorker,
                $model->createdAt,
                user: $user
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
            $user
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

    /**
     * Обрабатывает публикацию задачи
     *
     * @param PublishTaskService $publishTaskService
     * @param CategoryRepositoryInterface $categoryRepo
     * @return string|Response
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionPublish(
        PublishTaskService $publishTaskService,
        CategoryRepositoryInterface $categoryRepo,
    ): string|Response {
        $formModel = new PublishForm();
        $categories = array_map(fn(Category $category) => ['id' => $category->getId(), 'name' => $category->getName()],
            $categoryRepo->getAll());

        $categories = ArrayHelper::map($categories, 'id', 'name');

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
            $formModel->latitude,
            $formModel->longitude,
            $formModel->additionalInfo,
            $formModel->budget,
            Yii::$app->user->identity->getUser()->getCity()->getId(),
            $files,
        );

        $id = $publishTaskService->publish($saveTaskDTO);

        if (null === $id) {
            throw new RuntimeException('Не удалось создать задачу');
        }

        $newTask = Url::to(['/task/view', 'id' => $id]);
        return $this->redirect($newTask);
    }

    /**
     * Обрабатывает отклик к задаче
     *
     * @param int $id
     * @param TaskResponseService $responseService
     * @return array|Response
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
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
            (int)$completeForm->price,
        );

        $responseService->createResponse($saveResponseDTO);

        return $this->redirect(['task/view', 'id' => $id]);
    }

    /**
     * Обрабатывает отказ для отклика по заданию
     *
     * @param int $id
     * @param TaskResponseService $responseService
     * @param TaskResponseRepositoryInterface $taskResponseRepository
     * @return Response
     */
    public function actionRejectResponse(
        int $id,
        TaskResponseService $responseService,
        TaskResponseRepositoryInterface $taskResponseRepository
    ): Response {
        $taskId = $taskResponseRepository->getTaskIdByResponseId($id);
        $responseService->rejectResponse($id);

        return $this->redirect(['task/view', 'id' => $taskId]);
    }

    /**
     * Обрабатывает выбор исполнителя по заданию
     *
     * @param int $id
     * @param StartTaskService $service
     * @param TaskResponseRepositoryInterface $taskResponseRepository
     * @return Response
     * @throws NotFoundHttpException
     */
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

    /**
     * Обрабатывает заверение задания
     *
     * @param int $id
     * @param FinishTaskService $service
     * @return array|Response
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
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
            (int)$completeForm->rating,
            $completeForm->comment,
            $id,
            Yii::$app->user->identity->getUser()->getId(),
        );

        $service->handle($saveReviewDTO);

        return $this->redirect(['task/view', 'id' => $id]);
    }

    /**
     * Обрабатывает отмену задания
     *
     * @param int $id
     * @param CancelTaskService $service
     * @return array|Response
     * @throws NotFoundHttpException
     */
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

    /**
     * Обрабатывает отказ исполнителя от задания
     *
     * @param int $id
     * @param FailTaskService $service
     * @return array|Response
     * @throws NotFoundHttpException
     */
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

    /**
     * Отображает страницу "Мои задания" с фильтрацией по статусу
     *
     * @param MyTasksService $myTasksService
     * @return string
     */
    public function actionMy(MyTasksService $myTasksService): string
    {
        $activeTab = Yii::$app->request->get('tab', 'new');
        $userId = Yii::$app->user->id;

        if (!in_array($activeTab, ['new', 'in-progress', 'closed'])) {
            $activeTab = 'new';
        }

        $taskDTOs = $myTasksService->findTasksForUser($userId, $activeTab);

        $tabTitles = [
            'new' => 'Новые',
            'in-progress' => 'В процессе',
            'closed' => 'Закрытые',
        ];

        $provider = new ArrayDataProvider([
            'allModels' => $taskDTOs,
        ]);

        return $this->render('my', [
            'tasks' => $provider,
            'activeTab' => $activeTab,
            'tabTitles' => $tabTitles,
        ]);
    }
}
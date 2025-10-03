<?php

namespace app\controllers;

use app\models\ProfileEditForm;
use app\models\SecurityForm;
use Xvlvv\DTO\ChangePasswordDTO;
use Xvlvv\DTO\UpdateProfileDTO;
use Xvlvv\Entity\Category;
use Xvlvv\Repository\CategoryRepositoryInterface;
use Xvlvv\Repository\UserRepositoryInterface;
use Xvlvv\Services\Application\ProfileEditService;
use Xvlvv\Services\Application\SecurityService;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

/**
 * Контроллер для управления настройками профиля и безопасности пользователя.
 */
final class SettingsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['profile'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['security'],
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    /**
     * Отображает и обрабатывает форму редактирования профиля для исполнителя.
     * Если пользователь не исполнитель, перенаправляет на страницу безопасности.
     *
     * @param UserRepositoryInterface $userRepository
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ProfileEditService $profileEditService
     * @return Response|string
     * @throws ServerErrorHttpException
     */
    public function actionProfile(
        UserRepositoryInterface $userRepository,
        CategoryRepositoryInterface $categoryRepository,
        ProfileEditService $profileEditService
    ): Response|string {
        if (!Yii::$app->user->can('applyToTask')) {
            return $this->redirect(['settings/security']);
        }

        $user = $userRepository->getByIdOrFail(Yii::$app->user->id);
        $formModel = new ProfileEditForm($user);

        $categories = array_map(fn(Category $category) => ['id' => $category->getId(), 'name' => $category->getName()],
            $categoryRepository->getAll());

        $categories = ArrayHelper::map($categories, 'id', 'name');

        if (!Yii::$app->request->isPost) {
            return $this->render('profile', [
                'model' => $formModel,
                'user' => $user,
                'categories' => $categories,
            ]);
        }

        $formModel->load(Yii::$app->request->post());
        $formModel->avatarFile = UploadedFile::getInstance($formModel, 'avatarFile');

        if (!$formModel->validate()) {
            return $this->render('profile', [
                'model' => $formModel,
                'user' => $user,
                'categories' => $categories,
            ]);
        }

        $specializations = $formModel->specializations === '' ? [] : $formModel->specializations;

        $dto = new UpdateProfileDTO(
            $formModel->name,
            $formModel->email,
            $formModel->birthday,
            $formModel->phone,
            $formModel->telegram,
            $formModel->bio,
            $specializations,
            $formModel->avatarFile
        );

        $profileEditService->update($user->getId(), $dto);
        return $this->refresh();
    }

    /**
     * Отображает и обрабатывает форму смены пароля.
     *
     * @param SecurityService $securityService Сервис для смены пароля.
     * @return Response|string
     */
    public function actionSecurity(SecurityService $securityService): Response|string
    {
        $formModel = new SecurityForm();

        if (Yii::$app->request->isPost && $formModel->load(Yii::$app->request->post()) && $formModel->validate()) {
            $dto = new ChangePasswordDTO($formModel->newPassword);
            $securityService->changePassword(Yii::$app->user->id, $dto);

            Yii::$app->session->setFlash('success', 'Пароль успешно изменен!');
            return $this->refresh();
        }

        return $this->render('security', [
            'model' => $formModel,
        ]);
    }
}
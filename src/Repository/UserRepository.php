<?php

declare(strict_types=1);

namespace Xvlvv\Repository;

use app\models\CustomerProfile as CustomerProfileModel;
use app\models\ExecutorProfile;
use app\models\Review;
use app\models\User as UserModel;
use app\models\UserSpecialization;
use Exception;
use LogicException;
use RuntimeException;
use Throwable;
use Xvlvv\DataMapper\UserMapper;
use Xvlvv\DTO\UserReviewViewDTO;
use Xvlvv\DTO\UserSpecializationDTO;
use Xvlvv\DTO\ViewUserDTO;
use Xvlvv\Entity\Category;
use Xvlvv\Entity\CustomerProfile;
use Xvlvv\Entity\User;
use Xvlvv\Entity\WorkerProfile;
use Xvlvv\Enums\UserRole;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\web\NotFoundHttpException;

/**
 * Репозиторий для работы с сущностями User
 */
readonly final class UserRepository implements UserRepositoryInterface
{
    /**
     * @param ReviewRepositoryInterface $reviewRepo
     * @param TaskRepositoryInterface $taskRepo
     * @param UserMapper $userMapper
     */
    public function __construct(
        private ReviewRepositoryInterface $reviewRepo,
        private TaskRepositoryInterface $taskRepo,
        private UserMapper $userMapper,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getByIdOrFail(int $id): User
    {
        $user = $this->getById($id);

        if (null === $user) {
            throw new NotFoundHttpException('');
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function getById(int $id): ?User
    {
        $userModel = UserModel::findOne($id);

        if (null === $userModel) {
            return null;
        }

        return $this->userMapper->toDomainEntity($userModel);
    }

    /**
     * {@inheritDoc}
     */
    public function getByEmailOrFail(string $email): User
    {
        $user = $this->getByEmail($email);

        if (null === $user) {
            throw new NotFoundHttpException();
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function getByEmail(string|null $email): ?User
    {
        if (null === $email) {
            return null;
        }

        $userModel = UserModel::find()
            ->with(
                [
                    'city',
                    'workerProfile',
                    'customerProfile',
                ]
            )
            ->where(['email' => $email])->one();

        if (null === $userModel) {
            return null;
        }

        return $this->userMapper->toDomainEntity($userModel);
    }

    /**
     * {@inheritDoc}
     */
    public function update(User $user): void
    {
        if (null === $user->getId()) {
            throw new LogicException('Нельзя обновить несуществующего пользователя');
        }

        $this->save($user);
    }

    /**
     * {@inheritDoc}
     */
    public function save(User $user): User
    {
        $userModel = $this->toActiveRecord($user);

        try {
            $userModel->save();
        } catch (Throwable) {
            throw new RuntimeException('Ошибка при сохранении');
        }

        $user->setId($userModel->id);

        $profileEntity = $user->getProfile();

        $profileModelClass = match (get_class($profileEntity)) {
            WorkerProfile::class => ExecutorProfile::class,
            CustomerProfile::class => CustomerProfileModel::class,
            default => throw new LogicException('Неизвестный тип профиля'),
        };

        if ($user->getProfile() instanceof WorkerProfile) {
            /** @var WorkerProfile $profile */
            $profile = $user->getProfile();
            $specializationIds = array_map(
                fn(Category $spec) => $spec->getId(),
                $profile->getSpecializations()
            );
            $this->updateSpecializations($user->getId(), $specializationIds);
        }

        $profileModel = $profileModelClass::findOne(['user_id' => $user->getId()]);

        if ($profileModel === null) {
            $profileModel = new $profileModelClass();
            $profileModel->user_id = $user->getId();
        }

        if ($user->getProfile() instanceof WorkerProfile) {
            /** @var WorkerProfile $profile */
            $profile = $user->getProfile();
            $profileModel->day_of_birth = $profile->getDayOfBirth();
            $profileModel->bio = $profile->getBio();
            $profileModel->phone_number = $profile->getPhoneNumber();
            $profileModel->telegram_username = $profile->getTelegramUsername();
        }

        if (!$profileModel->save()) {
            throw new Exception();
        }

        $auth = Yii::$app->authManager;
        $roleName = $user->getUserRole()->value;
        $userId = $user->getId();

        if ($auth->getAssignment($roleName, $userId)) {
            return $user;
        }

        $authorRole = $auth->getRole($roleName);
        if ($authorRole) {
            $auth->assign($authorRole, $userId);
        }

        return $user;
    }

    /**
     * Преобразует сущность User в модель ActiveRecord UserModel
     *
     * @param User $user Сущность пользователя
     * @return UserModel Модель ActiveRecord
     */
    private function toActiveRecord(User $user): UserModel
    {
        $userModel = null !== $user->getId()
            ? UserModel::findOne($user->getId())
            : new UserModel();

        if (!$userModel) {
            throw new \RuntimeException('Cannot update user that don\'t exists');
        }

        $userModel->id = $user->getId();
        $userModel->name = $user->getName();
        $userModel->email = $user->getEmail();
        $userModel->password_hash = $user->getPasswordHash();
        $userModel->role = $user->getUserRole()->value;
        $userModel->city_id = $user->getCity()->getId();
        $userModel->access_token = $user->getAccessToken();
        $userModel->vk_id = $user->getVkId();
        $userModel->avatar_path = $user->getAvatarPath();

        return $userModel;
    }

    private function updateSpecializations(int $userId, array $newSpecializationIds): void
    {
        UserSpecialization::deleteAll(['user_id' => $userId]);

        if (empty($newSpecializationIds)) {
            return;
        }

        foreach ($newSpecializationIds as $categoryId) {
            try {
                $specialization = new UserSpecialization();
                $specialization->user_id = $userId;
                $specialization->category_id = $categoryId;
                $specialization->save();
            } catch (Throwable) {
                throw new RuntimeException('Ошибка при сохранении специализации');
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isUserExistsByEmail(string $email): bool
    {
        return UserModel::find()->where(['email' => $email])->exists();
    }

    /**
     * {@inheritDoc}
     */
    public function getWorkerForView(int $id): ViewUserDTO
    {
        $user = $this->getModelByIdOrFail($id, UserRole::WORKER);

        $userSpecializations = array_map(
            function ($categoryModel) {
                return new UserSpecializationDTO(
                    $categoryModel->id,
                    $categoryModel->name,
                );
            },
            $user->specializationCategories
        );

        $reviews = array_map(
            function ($reviewModel) {
                return new UserReviewViewDTO(
                    $reviewModel->task->id,
                    $reviewModel->task->name,
                    $reviewModel->comment,
                    $reviewModel->rating,
                    $reviewModel->created_at,
                    $reviewModel->customer->avatar_path
                );
            },
            $this->reviewRepo->getReviewsByUser($user->id)
        );

        $workerHasActiveTask = $this->taskRepo->workerHasActiveTask($user->id);
        $completedTasks = $this->taskRepo->getCompletedTasksCountByWorkerId($user->id);
        $rank = $this->getUserRank($user->id);

        $status = $workerHasActiveTask ? 'Открыт для новых заказов' : 'Занят';

        return new ViewUserDTO(
            $user->name,
            $user->avatar_path,
            $user->workerProfile->description,
            $user->workerProfile->bio,
            $userSpecializations,
            $completedTasks,
            $user->failedTasksCount,
            $user->rating,
            $rank,
            $user->created_at,
            $status,
            $user->workerProfile->phone_number,
            $user->email,
            $user->workerProfile->telegram_username,
            true,
            $reviews
        );
    }

    /**
     * Находит модель UserModel по ID и роли или выбрасывает исключение
     *
     * @param int $id ID пользователя
     * @param UserRole $role Роль пользователя
     * @return UserModel Модель ActiveRecord
     * @throws NotFoundHttpException если пользователь не найден
     */
    private function getModelByIdOrFail(int $id, UserRole $role): UserModel
    {
        $user = UserModel::findWithRating()->where(['id' => $id, 'role' => $role])->one();

        if (null === $user) {
            throw new NotFoundHttpException();
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function getUserRank(int $userId): int
    {
        $ratingsSubQuery = UserModel::find()
            ->select([
                'id' => '{{%user}}.id',
                'avg_rating' => new Expression('IFNULL(AVG({{%review}}.rating), 0)')
            ])
            ->leftJoin(Review::tableName(), '{{%review}}.worker_id = {{%user}}.id')
            ->where(['{{%user}}.role' => UserRole::WORKER])
            ->groupBy('{{%user}}.id');

        $rankedUsersQuery = (new Query())
            ->select([
                'id',
                'user_rank' => new Expression('RANK() OVER (ORDER BY avg_rating DESC)')
            ])
            ->from(['ratings' => $ratingsSubQuery]);

        $finalQuery = (new Query())
            ->select('user_rank')
            ->from(['ranked_users' => $rankedUsersQuery])
            ->where(['id' => $userId]);

        $rank = $finalQuery->scalar();

        if (null === $rank) {
            throw new LogicException('Cannot calculate rank for not worker type user');
        }

        return $rank;
    }

    public function getByVkId(int $vkId): ?User
    {
        $userModel = UserModel::find()
            ->with(['city', 'workerProfile', 'customerProfile'])
            ->where(['vk_id' => $vkId])->one();

        if (null === $userModel) {
            return null;
        }

        return $this->userMapper->toDomainEntity($userModel);
    }
}
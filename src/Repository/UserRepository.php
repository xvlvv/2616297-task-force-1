<?php

declare(strict_types = 1);

namespace Xvlvv\Repository;

use app\models\ExecutorProfile;
use app\models\Review;
use http\Exception\RuntimeException;
use LogicException;
use Throwable;
use Xvlvv\DataMapper\UserMapper;
use Xvlvv\DTO\UserReviewViewDTO;
use Xvlvv\DTO\UserSpecializationDTO;
use Xvlvv\DTO\ViewUserDTO;
use Xvlvv\Entity\CustomerProfile;
use app\models\CustomerProfile as CustomerProfileModel;
use Xvlvv\Entity\User;
use app\models\User as UserModel;
use Xvlvv\Entity\WorkerProfile;
use Xvlvv\Enums\UserRole;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\web\NotFoundHttpException;

/**
 * Репозиторий для работы с сущностями User
 */
class UserRepository implements UserRepositoryInterface
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
    public function getByEmailOrFail(string $email): User
    {
        // TODO: Implement getByEmailOrFail() method.
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
        } catch (Throwable $e) {
            throw new RuntimeException('Ошибка при сохранении');
        }

        $user->setId($userModel->id);

        $profileEntity = $user->getProfile();

        $profileModelClass = match (get_class($profileEntity)) {
            WorkerProfile::class => ExecutorProfile::class,
            CustomerProfile::class => CustomerProfileModel::class,
            default => throw new LogicException('Неизвестный тип профиля'),
        };

        $profileModel = new $profileModelClass();
        $profileModel->user_id = $user->getId();

        if (!$profileModel->save()) {
            throw new RuntimeException('Ошибка сохранения профиля пользователя');
        }

        $auth = Yii::$app->authManager;
        $authorRole = $auth->getRole($user->getUserRole()->value);
        $auth->assign($authorRole, $user->getId());
        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function isUserExistsByEmail(string $email): bool
    {
        return UserModel::find()->where(['email' => $email])->exists();
    }

    /**
     * Преобразует сущность User в модель ActiveRecord UserModel
     *
     * @param User $user Сущность пользователя
     * @return UserModel Модель ActiveRecord
     */
    private function toActiveRecord(User $user): UserModel
    {
        if (null !== $user->getId()) {
            return UserModel::findOne($user->getId());
        }

        $userModel = new UserModel();
        $userModel->id = $user->getId();
        $userModel->name = $user->getName();
        $userModel->email = $user->getEmail();
        $userModel->password_hash = $user->getPasswordHash();
        $userModel->role = $user->getUserRole()->value;
        $userModel->city_id = $user->getCity()->getId();
        $userModel->access_token = $user->getAccessToken();

        return $userModel;
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
     * {@inheritDoc}
     */
    public function getByEmail(string $email): ?User
    {
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

    public function isAuthor(int $userId): bool
    {
        $user = $this->getByIdOrFail($userId);

        return $user->getUserRole() === UserRole::CUSTOMER;
    }

    public function isWorker(int $userId): bool
    {
        $user = $this->getByIdOrFail($userId);

        return $user->getUserRole() === UserRole::WORKER;
    }
}
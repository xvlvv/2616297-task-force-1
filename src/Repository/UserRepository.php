<?php

declare(strict_types = 1);

namespace Xvlvv\Repository;

use app\models\Review;
use http\Exception\RuntimeException;
use LogicException;
use Throwable;
use Xvlvv\DTO\UserReviewViewDTO;
use Xvlvv\DTO\UserSpecializationDTO;
use Xvlvv\DTO\ViewUserDTO;
use Xvlvv\Entity\User;
use \app\models\User as UserModel;
use Xvlvv\Enums\UserRole;
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
     */
    public function __construct(
        private ReviewRepositoryInterface $reviewRepo,
        private TaskRepositoryInterface $taskRepo,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getById(int $id): ?User
    {
        // TODO: Implement getById() method.
    }

    /**
     * {@inheritDoc}
     */
    public function getByIdOrFail(int $id): User
    {
        // TODO: Implement getByIdOrFail() method.
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
        $userModel->name = $user->getName();
        $userModel->email = $user->getEmail();
        $userModel->password_hash = $user->getPasswordHash();
        $userModel->role = $user->getUserRole();
        $userModel->city_id = $user->getCity()->getId();

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
}
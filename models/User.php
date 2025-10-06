<?php

namespace app\models;

use Xvlvv\Enums\Status;
use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;

/**
 * ActiveRecord модель для таблицы "{{%user}}".
 * Представляет пользователя системы.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password_hash
 * @property string $role
 * @property int $city_id
 * @property string|null $avatar_path
 * @property string|null $access_token
 * @property int|null $vk_id
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property ExecutorProfile $workerProfile
 * @property CustomerProfile $customerProfile
 * @property Review[] $reviews
 * @property City $city
 * @property UserSpecialization[] $userSpecializations
 * @property Category[] $specializationCategories
 *
 * @property float $rating Виртуальное свойство для рейтинга
 * @property int $reviewsCount Виртуальное свойство для количества отзывов
 * @property int $failedTasksCount Виртуальное свойство для количества проваленных заданий
 */
class User extends ActiveRecord
{
    public float $rating = 0;
    public int $reviewsCount = 0;
    public int $failedTasksCount = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'email', 'password_hash', 'role'], 'required'],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['role'], 'in', 'range' => ['customer', 'worker']],
        ];
    }

    /**
     * Возвращает ID пользователя.
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Определяет связь с профилем исполнителя.
     * @return ActiveQuery
     */
    public function getWorkerProfile(): ActiveQuery
    {
        return $this->hasOne(ExecutorProfile::class, ['user_id' => 'id']);
    }

    /**
     * Определяет связь с профилем заказчика.
     * @return ActiveQuery
     */
    public function getCustomerProfile(): ActiveQuery
    {
        return $this->hasOne(CustomerProfile::class, ['user_id' => 'id']);
    }

    /**
     * Определяет связь с отзывами, где пользователь является исполнителем.
     * @return ActiveQuery
     */
    public function getReviews(): ActiveQuery
    {
        return $this->hasMany(Review::class, ['worker_id' => 'id']);
    }

    /**
     * Определяет связь с городом.
     * @return ActiveQuery
     */
    public function getCity(): ActiveQuery
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * Возвращает ActiveQuery с вычисляемыми полями рейтинга и количества отзывов/провалов.
     * @return ActiveQuery
     */
    public static function findWithRating(): ActiveQuery
    {
        $ratingSumSubQuery = (new Query())
            ->select('SUM(rating)')
            ->from(Review::tableName())
            ->where('worker_id = u.id');

        $reviewsCountSubQuery = (new Query())
            ->select('COUNT(*)')
            ->from(Review::tableName())
            ->where('worker_id = u.id');

        $failedTasksCountSubQuery = (new Query())
            ->select('COUNT(*)')
            ->from(Task::tableName())
            ->where('worker_id = u.id')
            ->andWhere(['status' => Status::FAILED]);

        $innerQuery = (new Query())
            ->select([
                'u.*',
                'ratingSum' => $ratingSumSubQuery,
                'reviewsCount' => $reviewsCountSubQuery,
                'failedTasksCount' => $failedTasksCountSubQuery,
            ])
            ->from(['u' => static::tableName()]);

        $query = new ActiveQuery(static::class);

        $query->select([
            'derived_table.*',
            'rating' => new Expression(
                'IF(
                    (derived_table.reviewsCount + derived_table.failedTasksCount) > 0,
                    IFNULL(derived_table.ratingSum, 0) / (derived_table.reviewsCount + derived_table.failedTasksCount),
                    0
                )'
            ),
            'reviewsCount' => 'derived_table.reviewsCount',
            'failedTasksCount' => 'derived_table.failedTasksCount',
        ])
            ->from(['derived_table' => $innerQuery]);

        return $query;
    }

    /**
     * Определяет связь со специализациями пользователя (промежуточная таблица).
     * @return ActiveQuery
     */
    public function getUserSpecializations(): ActiveQuery
    {
        return $this->hasMany(UserSpecialization::class, ['user_id' => 'id']);
    }

    /**
     * Определяет связь с категориями через промежуточную таблицу специализаций.
     * @return ActiveQuery
     */
    public function getSpecializationCategories(): ActiveQuery
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])
            ->via('userSpecializations');
    }

    /**
     * Генерирует токен доступа перед сохранением новой записи.
     * @param bool $insert
     * @return bool
     * @throws Exception
     */
    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->access_token = Yii::$app->security->generateRandomString();
            }
            return true;
        }
        return false;
    }
}

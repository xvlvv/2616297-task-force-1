<?php

namespace app\models;

use Xvlvv\Enums\Status;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;

class User extends ActiveRecord
{
    public float $rating = 0;
    public int $reviewsCount = 0;
    public int $failedTasksCount = 0;
    public static function tableName(): string
    {
        return '{{%user}}';
    }

    public function rules(): array
    {
        return [
            [['name', 'email', 'password_hash', 'role'], 'required'],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['role'], 'in', 'range' => ['customer', 'worker']],
        ];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === $password;
    }

    public function getWorkerProfile(): ActiveQuery
    {
        return $this->hasOne(ExecutorProfile::class, ['user_id' => 'id']);
    }

    public function getCustomerProfile(): ActiveQuery
    {
        return $this->hasOne(CustomerProfile::class, ['user_id' => 'id']);
    }

    public function getReviews(): ActiveQuery
    {
        return $this->hasMany(Review::class, ['worker_id' => 'id']);
    }

    public function getCity(): ActiveQuery
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }


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

    public function getUserSpecializations(): ActiveQuery
    {
        return $this->hasMany(UserSpecialization::class, ['user_id' => 'id']);
    }

    public function getSpecializationCategories(): ActiveQuery
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])
            ->via('userSpecializations');
    }
}

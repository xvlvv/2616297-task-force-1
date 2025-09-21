<?php

namespace app\models;

use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%task}}".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $category_id
 * @property int $customer_id
 * @property int|null $worker_id
 * @property int|null $city_id
 * @property string $status
 * @property int|null $budget
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $end_date
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Category $category
 * @property City $city
 * @property User $customer
 * @property File[] $files
 * @property Review $review
 * @property TaskFile[] $taskFiles
 * @property TaskResponse[] $taskResponses
 * @property User $worker
 */
class Task extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%task}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['worker_id', 'city_id', 'budget', 'latitude', 'longitude', 'end_date'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'new'],
            [['name', 'description', 'category_id', 'customer_id'], 'required'],
            [['description'], 'string'],
            [['category_id', 'customer_id', 'worker_id', 'city_id', 'budget'], 'integer', 'min' => 1],
            [['latitude', 'longitude'], 'number'],
            [['end_date', 'created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 32],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['city_id' => 'id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['customer_id' => 'id']],
            [['worker_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['worker_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'description' => 'Описание',
            'category_id' => 'ID категории',
            'customer_id' => 'ID клиента',
            'worker_id' => 'ID исполнителя',
            'city_id' => 'ID города',
            'status' => 'Статус',
            'budget' => 'Бюджет',
            'latitude' => 'Широта',
            'longitude' => 'Долгота',
            'end_date' => 'Крайний срок',
            'created_at' => 'Создана',
            'updated_at' => 'Обновлена',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return ActiveQuery
     */
    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[City]].
     *
     * @return ActiveQuery
     */
    public function getCity(): ActiveQuery
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * Gets query for [[Customer]].
     *
     * @return ActiveQuery
     */
    public function getCustomer(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'customer_id']);
    }

    /**
     * Gets query for [[Files]].
     *
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getFiles(): ActiveQuery
    {
        return $this->hasMany(File::class, ['id' => 'file_id'])->viaTable('{{%task_file}}', ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Review]].
     *
     * @return ActiveQuery
     */
    public function getReview(): ActiveQuery
    {
        return $this->hasOne(Review::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[TaskFiles]].
     *
     * @return ActiveQuery
     */
    public function getTaskFiles(): ActiveQuery
    {
        return $this->hasMany(TaskFile::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[TaskResponses]].
     *
     * @return ActiveQuery
     */
    public function getTaskResponses(): ActiveQuery
    {
        return $this->hasMany(TaskResponse::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Worker]].
     *
     * @return ActiveQuery
     */
    public function getWorker(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'worker_id']);
    }

}

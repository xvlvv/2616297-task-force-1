<?php

namespace Xvlvv\DataMapper;

use app\models\Category;
use app\models\Task;
use Xvlvv\Domain\ValueObject\Coordinates;
use Xvlvv\Entity\Category as CategoryEntity;
use Xvlvv\Entity\Task as TaskEntity;
use Xvlvv\Enums\Status;
use Xvlvv\Repository\CityRepository;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;
use yii\web\NotFoundHttpException;

/**
 * Маппер для преобразования данных между ActiveRecord моделью Task и доменной сущностью Task.
 */
final class TaskMapper
{
    /**
     * Преобразует ActiveRecord модель в доменную сущность.
     *
     * @param Task $task ActiveRecord модель задания.
     * @return TaskEntity Доменная сущность задания.
     * @throws NotFoundHttpException если у задания отсутствует категория.
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     */
    public function toDomainEntity(Task $task): TaskEntity
    {
        $repo = Yii::$container->get(CityRepository::class);

        $city = $repo->getById($task->customer->city_id);

        $status = Status::from($task->status);
        $category = Category::find()->where(['id' => $task->category->id])->one();

        $categoryEntity = new CategoryEntity(
            $task->category->id,
            $task->category->name
        );

        if (null === $category) {
            throw new NotFoundHttpException('Отсутствует категория у задачи');
        }

        if (null !== $task->latitude
            && null !== $task->longitude) {
            $coordinates = new Coordinates($task->latitude, $task->longitude);
        }

        return TaskEntity::create(
            $task->customer_id,
            $task->name,
            $task->description,
            $task->created_at,
            $categoryEntity,
            $task->end_date,
            $task->worker_id,
            $task->id,
            $task->budget,
            $status,
            $city,
            $coordinates ?? null,
            $task->location_info,
        );
    }
}
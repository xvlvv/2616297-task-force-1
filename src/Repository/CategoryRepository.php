<?php

declare(strict_types = 1);

namespace Xvlvv\Repository;

use Xvlvv\DataMapper\CategoryMapper;
use Xvlvv\Entity\Category;
use \app\models\Category as Model;
use yii\web\NotFoundHttpException;

/**
 * Репозиторий для работы с сущностями Category
 */
class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(
        private readonly CategoryMapper $mapper
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getById(int $id): ?Category
    {
        $category = Model::findOne($id);

        if (null === $category) {
            return null;
        }

        return $this->mapper->toDomainEntity($category);
    }

    public function getByIds(array $ids): array
    {
        $arModels = Model::find()->where(['id' => $ids])->all();

        $domainEntities = [];
        foreach ($arModels as $model) {
            $domainEntities[] = $this->mapper->toDomainEntity($model);
        }

        return $domainEntities;
    }

    /**
     * {@inheritdoc}
     */
    public function getByIdOrFail(int $id): Category
    {
        $category = $this->getById($id);

        if (null === $category) {
            throw new NotFoundHttpException('Category not found');
        }

        return $category;
    }

    public function getAll(): array
    {
        $arModels = Model::find()->select(['id', 'name'])->all();

        $domainEntities = [];

        foreach ($arModels as $model) {
            $domainEntities[] = $this->mapper->toDomainEntity($model);
        }

        return $domainEntities;
    }
}
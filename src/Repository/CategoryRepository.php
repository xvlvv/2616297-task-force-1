<?php

declare(strict_types = 1);

namespace Xvlvv\Repository;

use Xvlvv\Entity\Category;
use \app\models\Category as Model;
use yii\web\NotFoundHttpException;

/**
 * Репозиторий для работы с сущностями Category
 */
class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getById(int $id): ?Category
    {
        $category = Model::findOne($id);

        if (null === $category) {
            return null;
        }

        return new Category(
            $category->id,
            $category->name,
        );
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
        return Model::find()->select(['id', 'name'])->all();
    }
}
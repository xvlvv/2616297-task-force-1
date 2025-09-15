<?php

namespace Xvlvv\Repository;

use Xvlvv\Entity\Category;
use \app\models\Category as Model;
use yii\web\NotFoundHttpException;

class CategoryRepository implements CategoryRepositoryInterface
{

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

    public function getByIdOrFail(int $id): Category
    {
        $category = $this->getById($id);

        if (null === $category) {
            throw new NotFoundHttpException('Category not found');
        }

        return $category;
    }
}
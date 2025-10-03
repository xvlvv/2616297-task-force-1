<?php

declare(strict_types = 1);

namespace Xvlvv\Repository;

use Xvlvv\Entity\Category;
use yii\web\NotFoundHttpException;

/**
 * Интерфейс репозитория для категорий
 */
interface CategoryRepositoryInterface
{
    /**
     * Находит категорию по ID
     * @param int $id
     * @return Category|null
     */
    public function getById(int $id): ?Category;

    /**
     * Находит категорию по ID или выбрасывает исключение
     * @param int $id
     * @return Category
     * @throws NotFoundHttpException
     */
    public function getByIdOrFail(int $id): Category;
    public function getAll(): array;
}
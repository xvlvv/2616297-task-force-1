<?php

declare(strict_types = 1);

namespace Xvlvv\Repository;

use Xvlvv\Entity\City;
use yii\web\NotFoundHttpException;

/**
 * Интерфейс репозитория для городов
 */
interface CityRepositoryInterface
{
    /**
     * Находит город по ID
     * @param int $id
     * @return City|null
     */
    public function getById(int $id): ?City;

    /**
     * Находит город по ID или выбрасывает исключение
     * @param int $id
     * @return City
     * @throws NotFoundHttpException
     */
    public function getByIdOrFail(int $id): City;
}
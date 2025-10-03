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
     * Находит город по его уникальному идентификатору (ID).
     *
     * @param int $id ID города.
     * @return City|null Возвращает сущность City, если город найден, иначе null.
     */
    public function getById(int $id): ?City;

    /**
     * Находит город по его ID или выбрасывает исключение, если город не найден.
     *
     * @param int $id ID города.
     * @return City Возвращает сущность City.
     * @throws NotFoundHttpException если город с указанным ID не существует.
     */
    public function getByIdOrFail(int $id): City;

    /**
     * Сохраняет изменения в данных города.
     * Может использоваться как для обновления существующего города, так и для создания нового.
     *
     * @param City $city Сущность города с измененными данными.
     * @return void
     */
    public function update(City $city): void;
}
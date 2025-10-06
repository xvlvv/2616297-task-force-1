<?php

declare(strict_types=1);

namespace Xvlvv\DataMapper;

use app\models\City as CityModel;
use Xvlvv\Entity\City;

/**
 * Преобразует данные между ActiveRecord моделью City и доменной сущностью City.
 */
final class CityMapper
{
    /**
     * Преобразует ActiveRecord модель в доменную сущность.
     *
     * @param CityModel $arCity ActiveRecord модель города.
     * @return City Доменная сущность города.
     */
    public function toDomainEntity(CityModel $arCity): City
    {
        return new City(
            $arCity->id,
            $arCity->name,
            $arCity->bounding_box,
        );
    }

    /**
     * Преобразует доменную сущность в ActiveRecord модель.
     *
     * @param City $city Доменная сущность города.
     * @return CityModel ActiveRecord модель города.
     */
    public function toActiveRecord(City $city): CityModel
    {
        $cityModel = CityModel::findOne($city->getId());

        if (null === $cityModel) {
            $cityModel = new CityModel();
            $cityModel->id = $city->getId();
        }

        $cityModel->name = $city->getName();
        $cityModel->bounding_box = $city->getBoundingBox();

        return $cityModel;
    }
}
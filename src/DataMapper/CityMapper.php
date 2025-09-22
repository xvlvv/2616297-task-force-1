<?php

declare(strict_types = 1);

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
            $arCity->name
        );
    }
}
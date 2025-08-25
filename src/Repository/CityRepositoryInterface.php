<?php

namespace Xvlvv\Repository;

use Xvlvv\Entity\City;

interface CityRepositoryInterface
{
    public function getById(int $id): ?City;
    public function getByIdOrFail(int $id): City;
}
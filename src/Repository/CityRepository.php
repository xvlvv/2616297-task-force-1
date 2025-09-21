<?php

declare(strict_types = 1);

namespace Xvlvv\Repository;

use Xvlvv\Entity\City;
use \app\models\City as Model;
use Xvlvv\Repository\CityRepositoryInterface;
use yii\web\NotFoundHttpException;

/**
 * Репозиторий для работы с сущностями City
 */
class CityRepository implements CityRepositoryInterface
{

    /**
     * {@inheritdoc}
     */
    public function getById(int $id): ?City
    {
        $city = Model::findOne($id);

        if (null === $city) {
            return null;
        }

        return new City(
            $city->id,
            $city->name,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getByIdOrFail(int $id): City
    {
        $city = $this->getById($id);

        if (null === $city) {
            throw new NotFoundHttpException('City not found');
        }

        return $city;
    }
}
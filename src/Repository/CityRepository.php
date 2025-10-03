<?php

declare(strict_types = 1);

namespace Xvlvv\Repository;

use Xvlvv\DataMapper\CityMapper;
use Xvlvv\Entity\City;
use app\models\City as Model;
use yii\web\NotFoundHttpException;

/**
 * Репозиторий для работы с сущностями City
 */
readonly final class CityRepository implements CityRepositoryInterface
{
    public function __construct(
        private CityMapper $mapper
    ) {
    }

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
            $city->bounding_box,
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

    /**
     * {@inheritdoc}
     */
    public function update(City $city): void
    {
        $cityModel = $this->mapper->toActiveRecord($city);
        $cityModel->save();
    }
}
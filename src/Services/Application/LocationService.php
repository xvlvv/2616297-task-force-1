<?php

namespace Xvlvv\Services\Application;

use Xvlvv\Repository\UserRepository;
use Yii;

class LocationService
{
    public function __construct(
        private GeocoderInterface $geocoder
    ) {
    }

    public function searchForAutocomplete(?string $query, int $userId): array
    {
        if (empty($query)) {
            return [];
        }

        $repo = Yii::$container->get(UserRepository::class);

        $user = $repo->getByIdOrFail($userId);

        $city = $user->getCity();

        return $this->geocoder->findByAddress($query, $city);
    }
}
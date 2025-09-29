<?php

namespace Xvlvv\Services\Application;

use Xvlvv\Entity\City;

interface GeocoderInterface
{
    public function findByAddress(string $address, City $city): array;
}
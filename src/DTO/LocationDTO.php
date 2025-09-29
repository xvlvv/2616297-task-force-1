<?php

namespace Xvlvv\DTO;

final readonly class LocationDTO
{
    public function __construct(
        public string $fullAddress,
        public string $city,
        public ?string $additional,
        public string $latitude,
        public string $longitude,
    ) {
    }
}
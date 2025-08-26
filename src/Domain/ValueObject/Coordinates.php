<?php

namespace Xvlvv\Domain\ValueObject;

readonly final class Coordinates
{
    public function __construct(
        public string $latitude,
        public string $longitude
    ) {
    }
}
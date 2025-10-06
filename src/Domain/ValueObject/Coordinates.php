<?php

declare(strict_types=1);

namespace Xvlvv\Domain\ValueObject;

/**
 * Неизменяемый объект-значение, представляющий географические координаты
 *
 * @property-read string $latitude Географическая широта.
 * @property-read string $longitude Географическая долгота.
 */
readonly final class Coordinates
{
    /**
     * Конструктор Coordinates
     *
     * @param string $latitude Широта
     * @param string $longitude Долгота
     */
    public function __construct(
        public string $latitude,
        public string $longitude
    ) {
    }
}
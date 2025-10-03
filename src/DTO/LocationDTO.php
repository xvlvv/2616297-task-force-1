<?php

namespace Xvlvv\DTO;

/**
 * DTO для представления географической локации.
 * Используется сервисами геокодирования для возврата структурированных данных об адресе.
 */
final readonly class LocationDTO
{
    /**
     * @param string $fullAddress Полный адрес в виде строки.
     * @param string $city Название города.
     * @param string|null $additional Дополнительная информация (улица, дом).
     * @param string $latitude Географическая широта.
     * @param string $longitude Географическая долгота.
     */
    public function __construct(
        public string $fullAddress,
        public string $city,
        public ?string $additional,
        public string $latitude,
        public string $longitude,
    ) {
    }
}
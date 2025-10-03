<?php

namespace Xvlvv\Services\Application;

use Xvlvv\Entity\City;

/**
 * Интерфейс для сервисов геокодирования
 * Определяет контракт для поиска географических данных по текстовому адресу
 */
interface GeocoderInterface
{
    /**
     * Находит географические данные по адресу в пределах указанного города
     *
     * @param string $address Текстовый адрес для поиска (например, "Ленина 1")
     * @param City $city Сущность города, в границах которого нужно производить поиск
     * @return array Массив DTO с найденными локациями
     */
    public function findByAddress(string $address, City $city): array;
}
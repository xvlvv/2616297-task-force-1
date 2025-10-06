<?php

namespace Xvlvv\Services\Application;

use Xvlvv\Repository\UserRepositoryInterface;

/**
 * Сервис для работы с геолокацией
 * Используется для поиска адресов и автодополнения
 */
readonly final class LocationService
{
    /**
     * @param GeocoderInterface $geocoder Сервис для взаимодействия с API геокодера
     * @param UserRepositoryInterface $userRepository Репозиторий для получения данных пользователя
     */
    public function __construct(
        private GeocoderInterface $geocoder,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * Ищет локации для автодополнения на основе запроса и города пользователя
     *
     * @param string|null $query Строка поискового запроса
     * @param int $userId ID текущего пользователя
     * @return array Массив DTO с найденными локациями
     */
    public function searchForAutocomplete(?string $query, int $userId): array
    {
        if (empty($query)) {
            return [];
        }

        $user = $this->userRepository->getByIdOrFail($userId);
        $city = $user->getCity();

        return $this->geocoder->findByAddress($query, $city);
    }
}
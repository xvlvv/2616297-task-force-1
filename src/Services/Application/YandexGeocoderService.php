<?php

namespace Xvlvv\Services\Application;

use Throwable;
use Xvlvv\DTO\LocationDTO;
use Xvlvv\Entity\City;
use Xvlvv\Repository\CityRepositoryInterface;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;

/**
 * Сервис для взаимодействия с Yandex Geocoder API
 */
readonly class YandexGeocoderService implements GeocoderInterface
{
    private string $apiKey;

    /**
     * @param Client $httpClient HTTP-клиент для отправки запросов
     * @param CityRepositoryInterface $cityRepository Репозиторий для работы с городами
     * @throws InvalidConfigException если API-ключ не настроен
     */
    public function __construct(
        private Client $httpClient,
        private CityRepositoryInterface $cityRepository,
    )
    {
        if (!isset($_ENV['YANDEX_GEOCODER_API_KEY'])) {
            throw new InvalidConfigException('Yandex Geocoder API key is not configured');
        }

        $this->apiKey = $_ENV['YANDEX_GEOCODER_API_KEY'];
    }

    /**
     * {@inheritdoc}
     */
    public function findByAddress(string $address, City $city): array
    {
        $restriction = $city->getBoundingBox();

        if (null === $restriction) {
            $userCity = $this->makeRequest($city->getName(), results: 1);
            $envelope = $userCity['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['boundedBy']['Envelope'] ?? [];
        }

        if (isset($envelope, $envelope['lowerCorner'], $envelope['upperCorner'])) {
            $lowerCorner = str_replace(' ', ',', $envelope['lowerCorner']);
            $upperCorner = str_replace(' ', ',', $envelope['upperCorner']);

            $boundingBox = "{$lowerCorner}~{$upperCorner}";

            $city->updateBoundingBox($boundingBox);
            $this->cityRepository->update($city);
        }

        $address = "{$city->getName()}, $address";

        $result = $this->makeRequest($address, boundingBox: $restriction, rspn: 1);

        $locations = [];
        $featureMembers = $result['response']['GeoObjectCollection']['featureMember'] ?? [];

        foreach ($featureMembers as $member) {
            $geoObject = $member['GeoObject'] ?? null;
            if (!$geoObject) {
                continue;
            }

            $pos = $geoObject['Point']['pos'] ?? null;
            if (!$pos) {
                continue;
            }

            [$longitude, $latitude] = explode(' ', $pos);

            $addressParts = [];
            $components = $geoObject['metaDataProperty']['GeocoderMetaData']['Address']['Components'] ?? [];
            foreach ($components as $component) {
                $key = match ($component['kind']) {
                    'country'  => 'country',
                    'locality' => 'city',
                    'street'   => 'street',
                    'house'    => 'house',
                    default    => null,
                };

                if ($key) {
                    $addressParts[$key] = $component['name'];
                }
            }

            $city = $addressParts['city'] ?? null;
            if (null === $city) {
                continue;
            }

            $additionalParts = array_filter([
                $addressParts['street'] ?? null,
                $addressParts['house'] ?? null
            ]);
            $additional = !empty($additionalParts) ? implode(', ', $additionalParts) : null;

            $fullAddressParts = array_filter([
                $addressParts['country'] ?? null,
                $city,
                $additional
            ]);
            $fullAddress = implode(', ', $fullAddressParts);

            $locations[] = new LocationDTO(
                $fullAddress,
                $city,
                $additional,
                $latitude,
                $longitude,
            );
        }

        return $locations;
    }

    /**
     * Выполняет GET-запрос к Yandex Geocoder API
     *
     * @param string $address Адрес для геокодирования
     * @param string|null $boundingBox Ограничивающий прямоугольник для поиска
     * @param int $results Максимальное количество результатов
     * @param int $rspn Флаг для ограничения поиска границами из bbox
     * @return array Ответ от API в виде массива или пустой массив в случае ошибки
     */
    private function makeRequest(string $address, ?string $boundingBox = null, int $results = 10, int $rspn = 0): array
    {
        $requestParams = [
            'apikey' => $this->apiKey,
            'geocode' => $address,
            'format' => 'json',
            'rspn' => $rspn,
            'results' => $results,
        ];

        if (null !== $boundingBox) {
            $requestParams['bbox'] = $boundingBox;
        }

        try {
            $response = $this->httpClient->createRequest()
                ->setMethod('GET')
                ->setUrl('https://geocode-maps.yandex.ru/v1/')
                ->setData($requestParams)
                ->send();

            if (!$response->isOk) {
                return [];
            }
        } catch (
        Throwable
        ) {
            return [];
        }

        return $response->getData();
    }
}
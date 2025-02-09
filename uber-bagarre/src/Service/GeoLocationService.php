<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeoLocationService
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getCoordinates(string $city): ?array
    {
        $url = 'https://nominatim.openstreetmap.org/search?format=json&q=' . urlencode($city);
        $response = $this->httpClient->request('GET', $url);

        $data = $response->toArray();

        return !empty($data) ? ['lat' => $data[0]['lat'], 'lng' => $data[0]['lon']] : null;
    }
}

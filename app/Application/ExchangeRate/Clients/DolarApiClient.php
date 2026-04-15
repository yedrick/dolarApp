<?php

declare(strict_types=1);

namespace App\Application\ExchangeRate\Clients;

use App\Application\ExchangeRate\DTOs\DolarApiResponseDTO;
use Illuminate\Support\Facades\Http;

/**
 * Cliente HTTP para consumir la API de DolarApi (Bolivia).
 * SRP: Solo se encarga de la comunicación con la API externa.
 */
final class DolarApiClient
{
    private const BASE_URL = 'https://bo.dolarapi.com';

    /**
     * Obtiene el tipo de cambio oficial del dólar en Bolivia.
     * @throws \Exception
     */
    public function getOfficialRate(): DolarApiResponseDTO
    {
        $response = Http::get(self::BASE_URL . '/v1/dolares/oficial');

        if ($response->failed()) {
            throw new \Exception('Error al obtener dólar oficial: ' . $response->status());
        }

        $data = $response->json();

        return DolarApiResponseDTO::fromApiResponse($data);
    }

    /**
     * Obtiene el tipo de cambio de Binance (dólar paralelo).
     * @throws \Exception
     */
    public function getBinanceRate(): DolarApiResponseDTO
    {
        $response = Http::get(self::BASE_URL . '/v1/dolares/binance');

        if ($response->failed()) {
            throw new \Exception('Error al obtener dólar Binance: ' . $response->status());
        }

        $data = $response->json();

        return DolarApiResponseDTO::fromApiResponse($data);
    }
}

<?php

declare(strict_types=1);

namespace App\Application\ExchangeRate\UseCases;

use App\Application\ExchangeRate\Clients\DolarApiClient;
use App\Application\ExchangeRate\DTOs\DolarApiResponseDTO;
use App\Domain\ExchangeRate\Entities\ExchangeRate;
use App\Domain\ExchangeRate\Repositories\ExchangeRateRepositoryInterface;
use App\Domain\ExchangeRate\ValueObjects\ExchangeRateType;

/**
 * Caso de uso: Obtener tipos de cambio desde API externa y guardarlos en BD.
 * SRP: Solo orchestra la obtención desde API y persistencia.
 * OCP: Puede extenderse para soportar nuevas fuentes de API.
 */
final class FetchExchangeRatesFromApiUseCase
{
    public function __construct(
        private readonly DolarApiClient $apiClient,
        private readonly ExchangeRateRepositoryInterface $repository,
    ) {}

    /**
     * Ejecuta la obtención de tipos de cambio desde la API.
     * @return array<string, array{compra: float, venta: float, fuente: string}>
     */
    public function execute(): array
    {
        $results = [];

        // Obtener dólar oficial
        $officialRate = $this->fetchOfficialRate();
        if ($officialRate) {
            $results['oficial'] = $officialRate;
        }

        // Obtener dólar Binance (paralelo)
        $binanceRate = $this->fetchBinanceRate();
        if ($binanceRate) {
            $results['paralelo'] = $binanceRate;
        }

        // El librecambista lo mantenemos con valores por defecto o manual
        // ya que no hay API pública para ese dato
        $results['librecambista'] = [
            'compra' => 0,
            'venta' => 0,
            'fuente' => 'manual',
        ];

        return $results;
    }

    private function fetchOfficialRate(): ?array
    {
        try {
            $dto = $this->apiClient->getOfficialRate();

            $this->saveToDatabase('oficial', $dto->compra, $dto->venta, 'DolarApi');

            return [
                'compra' => $dto->compra,
                'venta' => $dto->venta,
                'fuente' => 'DolarApi',
            ];
        } catch (\Exception $e) {
            report($e);
            return null;
        }
    }

    private function fetchBinanceRate(): ?array
    {
        try {
            $dto = $this->apiClient->getBinanceRate();

            $this->saveToDatabase('paralelo', $dto->compra, $dto->venta, 'DolarApi-Binance');

            return [
                'compra' => $dto->compra,
                'venta' => $dto->venta,
                'fuente' => 'DolarApi-Binance',
            ];
        } catch (\Exception $e) {
            report($e);
            return null;
        }
    }

    private function saveToDatabase(string $type, float $buyPrice, float $sellPrice, string $source): void
    {
        $rate = ExchangeRate::create(
            type: ExchangeRateType::from($type),
            buyPrice: $buyPrice,
            sellPrice: $sellPrice,
            source: $source,
        );

        $this->repository->updateOrCreateByType($rate);
    }
}

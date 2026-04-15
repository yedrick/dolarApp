<?php

declare(strict_types=1);

namespace App\Application\ExchangeRate\UseCases;

use App\Domain\ExchangeRate\Repositories\ExchangeRateRepositoryInterface;
use App\Domain\ExchangeRate\ValueObjects\ExchangeRateType;
use App\Application\ExchangeRate\DTOs\ConversionResultDTO;
use App\Shared\Exceptions\ExchangeRateNotFoundException;

/**
 * Caso de uso: Convertir moneda.
 * Delega la lógica de conversión a la entidad de dominio.
 */
final class ConvertCurrencyUseCase
{
    public function __construct(
        private readonly ExchangeRateRepositoryInterface $repository,
    ) {}

    public function execute(float $amount, string $from, string $rateType): ConversionResultDTO
    {
        $type = ExchangeRateType::from($rateType);
        $rate = $this->repository->findByType($type);

        if ($rate === null) {
            throw new ExchangeRateNotFoundException(
                "No se encontró tipo de cambio para: {$rateType}"
            );
        }

        [$result, $direction] = match ($from) {
            'BOB' => [$rate->convertToDollars($amount), 'BOB → USD'],
            'USD' => [$rate->convertToBolivianos($amount), 'USD → BOB'],
            default => throw new \InvalidArgumentException("Moneda no soportada: {$from}"),
        };

        return new ConversionResultDTO(
            input:     $amount,
            output:    $result,
            direction: $direction,
            rateType:  $rateType,
            buyPrice:  $rate->buyPrice()->amount(),
            sellPrice: $rate->sellPrice()->amount(),
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Application\ExchangeRate\UseCases;

use App\Domain\ExchangeRate\Repositories\ExchangeRateRepositoryInterface;
use App\Application\ExchangeRate\DTOs\ExchangeRateDTO;

/**
 * Caso de uso: Obtener todos los tipos de cambio.
 * SRP: Solo orquesta la consulta de tipos de cambio.
 */
final class GetExchangeRatesUseCase
{
    public function __construct(
        private readonly ExchangeRateRepositoryInterface $repository,
    ) {}

    /** @return ExchangeRateDTO[] */
    public function execute(): array
    {
        $rates = $this->repository->findAll();

        return array_map(
            fn ($rate) => ExchangeRateDTO::fromEntity($rate),
            $rates,
        );
    }
}

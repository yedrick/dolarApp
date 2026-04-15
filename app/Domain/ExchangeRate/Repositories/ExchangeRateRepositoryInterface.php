<?php

declare(strict_types=1);

namespace App\Domain\ExchangeRate\Repositories;

use App\Domain\ExchangeRate\Entities\ExchangeRate;
use App\Domain\ExchangeRate\ValueObjects\ExchangeRateType;

/**
 * Contrato de repositorio (DIP: dominio depende de abstracción, no de Eloquent).
 * La implementación concreta vive en Infrastructure.
 */
interface ExchangeRateRepositoryInterface
{
    public function findByType(ExchangeRateType $type): ?ExchangeRate;

    /** @return ExchangeRate[] */
    public function findAll(): array;

    public function save(ExchangeRate $rate): ExchangeRate;

    public function updateOrCreateByType(ExchangeRate $rate): ExchangeRate;
}

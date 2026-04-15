<?php

declare(strict_types=1);

namespace App\Application\ExchangeRate\DTOs;

final class ConversionResultDTO
{
    public function __construct(
        public readonly float $input,
        public readonly float $output,
        public readonly string $direction,
        public readonly string $rateType,
        public readonly float $buyPrice,
        public readonly float $sellPrice,
    ) {}
}

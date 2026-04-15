<?php

declare(strict_types=1);

namespace App\Application\ExchangeRate\DTOs;

use App\Domain\ExchangeRate\Entities\ExchangeRate;

final class ExchangeRateDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $type,
        public readonly float $buyPrice,
        public readonly float $sellPrice,
        public readonly float $spread,
        public readonly string $source,
        public readonly string $updatedAt,
    ) {}

    public static function fromEntity(ExchangeRate $rate): self
    {
        return new self(
            id:        $rate->id(),
            type:      $rate->type()->value(),
            buyPrice:  $rate->buyPrice()->amount(),
            sellPrice: $rate->sellPrice()->amount(),
            spread:    $rate->spread(),
            source:    $rate->source(),
            updatedAt: $rate->updatedAt()->format('Y-m-d H:i:s'),
        );
    }
}

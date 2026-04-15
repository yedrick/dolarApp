<?php

declare(strict_types=1);

namespace App\Domain\ExchangeRate\Entities;

use App\Domain\ExchangeRate\ValueObjects\ExchangeRateType;
use App\Domain\ExchangeRate\ValueObjects\Money;
use DateTimeImmutable;

/**
 * Entidad de dominio: Tipo de Cambio
 * 
 * SRP: Solo gestiona la lógica de un tipo de cambio.
 * OCP: Extendible para nuevos tipos sin modificar esta clase.
 */
final class ExchangeRate
{
    private function __construct(
        private readonly ?int $id,
        private readonly ExchangeRateType $type,
        private readonly Money $buyPrice,
        private readonly Money $sellPrice,
        private readonly string $source,
        private readonly DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        ExchangeRateType $type,
        float $buyPrice,
        float $sellPrice,
        string $source,
    ): self {
        return new self(
            id: null,
            type: $type,
            buyPrice: Money::fromFloat($buyPrice),
            sellPrice: Money::fromFloat($sellPrice),
            source: $source,
            updatedAt: new DateTimeImmutable(),
        );
    }

    public static function reconstitute(
        int $id,
        ExchangeRateType $type,
        float $buyPrice,
        float $sellPrice,
        string $source,
        DateTimeImmutable $updatedAt,
    ): self {
        return new self($id, $type, Money::fromFloat($buyPrice), Money::fromFloat($sellPrice), $source, $updatedAt);
    }

    // ── Conversión ───────────────────────────────────────────────────────────

    /**
     * Convierte bolivianos a dólares usando el precio de compra.
     * Principio: lógica de negocio vive en el dominio, no en controllers.
     */
    public function convertToDollars(float $bolivianos): float
    {
        if ($this->buyPrice->isZero()) {
            throw new \DomainException('El precio de compra no puede ser cero.');
        }

        return round($bolivianos / $this->buyPrice->amount(), 2);
    }

    /**
     * Convierte dólares a bolivianos usando el precio de venta.
     */
    public function convertToBolivianos(float $dollars): float
    {
        return round($dollars * $this->sellPrice->amount(), 2);
    }

    /**
     * Diferencial (spread) entre compra y venta.
     */
    public function spread(): float
    {
        return round($this->sellPrice->amount() - $this->buyPrice->amount(), 4);
    }

    // ── Getters ──────────────────────────────────────────────────────────────

    public function id(): ?int { return $this->id; }
    public function type(): ExchangeRateType { return $this->type; }
    public function buyPrice(): Money { return $this->buyPrice; }
    public function sellPrice(): Money { return $this->sellPrice; }
    public function source(): string { return $this->source; }
    public function updatedAt(): DateTimeImmutable { return $this->updatedAt; }
}

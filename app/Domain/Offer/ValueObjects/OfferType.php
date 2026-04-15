<?php

declare(strict_types=1);

namespace App\Domain\Offer\ValueObjects;

final class OfferType
{
    public const COMPRA = 'compra';
    public const VENTA  = 'venta';

    private function __construct(private readonly string $value) {}

    public static function from(string $value): self
    {
        if (!in_array($value, [self::COMPRA, self::VENTA], true)) {
            throw new \InvalidArgumentException("Tipo de oferta inválido: '{$value}'. Use 'compra' o 'venta'.");
        }

        return new self($value);
    }

    public static function compra(): self { return new self(self::COMPRA); }
    public static function venta(): self  { return new self(self::VENTA); }

    public function value(): string { return $this->value; }
    public function isCompra(): bool { return $this->value === self::COMPRA; }
    public function isVenta(): bool  { return $this->value === self::VENTA; }
    public function equals(self $other): bool { return $this->value === $other->value; }
    public function __toString(): string { return $this->value; }
}

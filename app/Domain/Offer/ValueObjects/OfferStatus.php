<?php

declare(strict_types=1);

namespace App\Domain\Offer\ValueObjects;

final class OfferStatus
{
    public const ACTIVA   = 'activa';
    public const CERRADA  = 'cerrada';
    public const EXPIRADA = 'expirada';

    private function __construct(private readonly string $value) {}

    public static function from(string $value): self
    {
        $valid = [self::ACTIVA, self::CERRADA, self::EXPIRADA];

        if (!in_array($value, $valid, true)) {
            throw new \InvalidArgumentException("Estado de oferta inválido: '{$value}'.");
        }

        return new self($value);
    }

    public static function active(): self  { return new self(self::ACTIVA); }
    public static function closed(): self  { return new self(self::CERRADA); }
    public static function expired(): self { return new self(self::EXPIRADA); }

    public function value(): string   { return $this->value; }
    public function isActive(): bool  { return $this->value === self::ACTIVA; }
    public function isClosed(): bool  { return $this->value === self::CERRADA; }
    public function isExpired(): bool { return $this->value === self::EXPIRADA; }

    public function __toString(): string { return $this->value; }
}

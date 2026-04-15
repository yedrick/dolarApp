<?php

declare(strict_types=1);

namespace App\Domain\ExchangeRate\ValueObjects;

/**
 * Value Object: tipo de cambio (inmutable, comparable por valor)
 * Tipos: oficial, paralelo, librecambista
 */
final class ExchangeRateType
{
    public const OFICIAL      = 'oficial';
    public const PARALELO     = 'paralelo';
    public const LIBRECAMBISTA = 'librecambista';

    private static array $valid = [self::OFICIAL, self::PARALELO, self::LIBRECAMBISTA];

    private function __construct(private readonly string $value) {}

    public static function from(string $value): self
    {
        if (!in_array($value, self::$valid, true)) {
            throw new \InvalidArgumentException(
                "Tipo de cambio inválido: '{$value}'. Válidos: " . implode(', ', self::$valid)
            );
        }

        return new self($value);
    }

    public static function oficial(): self      { return new self(self::OFICIAL); }
    public static function paralelo(): self     { return new self(self::PARALELO); }
    public static function librecambista(): self { return new self(self::LIBRECAMBISTA); }

    public function value(): string { return $this->value; }

    public function equals(self $other): bool { return $this->value === $other->value; }

    public function __toString(): string { return $this->value; }

    public static function all(): array { return self::$valid; }
}

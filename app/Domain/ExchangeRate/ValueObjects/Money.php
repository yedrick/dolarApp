<?php

declare(strict_types=1);

namespace App\Domain\ExchangeRate\ValueObjects;

/**
 * Value Object: dinero (inmutable, sin identidad propia)
 * Garantiza que los montos sean positivos y con precisión controlada.
 */
final class Money
{
    private function __construct(private readonly float $amount) {}

    public static function fromFloat(float $amount): self
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException("El monto no puede ser negativo: {$amount}");
        }

        return new self(round($amount, 4));
    }

    public function amount(): float { return $this->amount; }

    public function isZero(): bool { return $this->amount === 0.0; }

    public function add(self $other): self
    {
        return new self(round($this->amount + $other->amount, 4));
    }

    public function subtract(self $other): self
    {
        $result = round($this->amount - $other->amount, 4);

        if ($result < 0) {
            throw new \DomainException('La resta produce un monto negativo.');
        }

        return new self($result);
    }

    public function greaterThan(self $other): bool
    {
        return $this->amount > $other->amount;
    }

    public function equals(self $other): bool
    {
        return abs($this->amount - $other->amount) < 0.0001;
    }

    public function __toString(): string
    {
        return number_format($this->amount, 2, '.', ',');
    }
}

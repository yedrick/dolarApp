<?php

declare(strict_types=1);

namespace App\Domain\Offer\Entities;

use App\Domain\ExchangeRate\ValueObjects\Money;
use App\Domain\Offer\ValueObjects\OfferType;
use App\Domain\Offer\ValueObjects\OfferStatus;
use App\Domain\Offer\Events\OfferPublished;
use DateTimeImmutable;

/**
 * Aggregate Root: Oferta de compra/venta de divisas.
 * Contiene la lógica de negocio central de la oferta.
 */
final class Offer
{
    private array $domainEvents = [];

    private function __construct(
        private readonly ?int $id,
        private readonly int $userId,
        private readonly OfferType $type,
        private Money $price,
        private float $amount,
        private float $reservedAmount,
        private OfferStatus $status,
        private ?string $contactInfo,
        private ?string $notes,
        private readonly DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {}

    /**
     * Factory method: crea una nueva oferta (estado inicial: activa).
     */
    public static function publish(
        int $userId,
        string $type,
        float $price,
        float $amount,
        ?string $contactInfo,
        ?string $notes,
    ): self {
        $offer = new self(
            id: null,
            userId: $userId,
            type: OfferType::from($type),
            price: Money::fromFloat($price),
            amount: $amount,
            reservedAmount: 0,
            status: OfferStatus::active(),
            contactInfo: $contactInfo,
            notes: $notes,
            createdAt: new DateTimeImmutable(),
            updatedAt: new DateTimeImmutable(),
        );

        // Emitir evento de dominio
        $offer->domainEvents[] = new OfferPublished($userId, $type, $price);

        return $offer;
    }

    public static function reconstitute(
        int $id,
        int $userId,
        string $type,
        float $price,
        float $amount,
        float $reservedAmount,
        string $status,
        ?string $contactInfo,
        ?string $notes,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            $id, $userId,
            OfferType::from($type),
            Money::fromFloat($price),
            $amount,
            $reservedAmount,
            OfferStatus::from($status),
            $contactInfo, $notes,
            $createdAt, $updatedAt,
        );
    }

    // ── Comportamientos ──────────────────────────────────────────────────────

    public function close(): void
    {
        if ($this->status->isClosed()) {
            throw new \DomainException('La oferta ya está cerrada.');
        }

        $this->status     = OfferStatus::closed();
        $this->updatedAt  = new DateTimeImmutable();
    }

    public function updatePrice(float $newPrice): void
    {
        if (!$this->status->isActive()) {
            throw new \DomainException('Solo se puede modificar el precio de una oferta activa.');
        }

        $this->price     = Money::fromFloat($newPrice);
        $this->updatedAt = new DateTimeImmutable();
    }

    public function isExpired(int $maxHours = 72): bool
    {
        $diff = (new DateTimeImmutable())->getTimestamp() - $this->createdAt->getTimestamp();

        return ($diff / 3600) > $maxHours;
    }

    public function reserveAmount(float $amount): void
    {
        if (!$this->status->isActive()) {
            throw new \DomainException('No se puede reservar monto en una oferta inactiva.');
        }

        $available = $this->amount - $this->reservedAmount;
        if ($amount > $available) {
            throw new \DomainException('No hay suficiente monto disponible. Disponible: ' . $available);
        }

        $this->reservedAmount += $amount;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function releaseAmount(float $amount): void
    {
        if ($amount > $this->reservedAmount) {
            throw new \DomainException('No se puede liberar más monto del reservado.');
        }

        $this->reservedAmount -= $amount;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function availableAmount(): float
    {
        return $this->amount - $this->reservedAmount;
    }

    // ── Domain Events ────────────────────────────────────────────────────────

    public function pullDomainEvents(): array
    {
        $events            = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }

    // ── Getters ──────────────────────────────────────────────────────────────

    public function id(): ?int         { return $this->id; }
    public function userId(): int      { return $this->userId; }
    public function type(): OfferType  { return $this->type; }
    public function price(): Money     { return $this->price; }
    public function amount(): float    { return $this->amount; }
    public function reservedAmount(): float { return $this->reservedAmount; }
    public function status(): OfferStatus { return $this->status; }
    public function contactInfo(): ?string { return $this->contactInfo; }
    public function notes(): ?string   { return $this->notes; }
    public function createdAt(): DateTimeImmutable { return $this->createdAt; }
    public function updatedAt(): DateTimeImmutable { return $this->updatedAt; }
}

<?php

declare(strict_types=1);

namespace App\Domain\Offer\Events;

use DateTimeImmutable;

/**
 * Evento de dominio: Oferta publicada.
 * Puede ser escuchado por otros bounded contexts (notificaciones, estadísticas).
 */
final class OfferPublished
{
    public readonly DateTimeImmutable $occurredAt;

    public function __construct(
        public readonly int $userId,
        public readonly string $offerType,
        public readonly float $price,
    ) {
        $this->occurredAt = new DateTimeImmutable();
    }
}

<?php

declare(strict_types=1);

namespace App\Application\Offer\Commands;

/**
 * Command: datos necesarios para crear una oferta.
 * Inmutable, sin lógica, solo transporte de datos.
 */
final class CreateOfferCommand
{
    public function __construct(
        public readonly int $userId,
        public readonly string $type,
        public readonly float $price,
        public readonly float $amount,
        public readonly ?string $contactInfo,
        public readonly ?string $notes,
    ) {}
}

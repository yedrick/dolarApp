<?php

declare(strict_types=1);

namespace App\Application\Offer\DTOs;

use App\Domain\Offer\Entities\Offer;

final class OfferDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $userId,
        public readonly string $type,
        public readonly float $price,
        public readonly float $amount,
        public readonly float $reservedAmount,
        public readonly float $availableAmount,
        public readonly string $status,
        public readonly ?string $contactInfo,
        public readonly ?string $notes,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {}

    public static function fromEntity(Offer $offer): self
    {
        return new self(
            id:          $offer->id(),
            userId:      $offer->userId(),
            type:        $offer->type()->value(),
            price:       $offer->price()->amount(),
            amount:      $offer->amount(),
            reservedAmount: $offer->reservedAmount(),
            availableAmount: $offer->availableAmount(),
            status:      $offer->status()->value(),
            contactInfo: $offer->contactInfo(),
            notes:       $offer->notes(),
            createdAt:   $offer->createdAt()->format('Y-m-d H:i:s'),
            updatedAt:   $offer->updatedAt()->format('Y-m-d H:i:s'),
        );
    }
}

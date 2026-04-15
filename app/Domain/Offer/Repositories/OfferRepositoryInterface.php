<?php

declare(strict_types=1);

namespace App\Domain\Offer\Repositories;

use App\Domain\Offer\Entities\Offer;

interface OfferRepositoryInterface
{
    public function findById(int $id): ?Offer;

    /**
     * @param  array<string, mixed> $filters  Ej: ['type' => 'compra', 'status' => 'activa']
     * @return Offer[]
     */
    public function findAll(array $filters = [], int $perPage = 15): array;

    public function findByUser(int $userId): array;

    public function save(Offer $offer): Offer;

    public function delete(int $id): void;

    public function countActiveByUser(int $userId): int;
}

<?php

declare(strict_types=1);

namespace App\Application\Offer\UseCases;

use App\Domain\Offer\Repositories\OfferRepositoryInterface;
use App\Application\Offer\DTOs\OfferDTO;

/**
 * Caso de uso: Listar ofertas con filtros opcionales.
 */
final class ListOffersUseCase
{
    public function __construct(
        private readonly OfferRepositoryInterface $repository,
    ) {}

    /** @return OfferDTO[] */
    public function execute(array $filters = [], int $perPage = 15): array
    {
        $offers = $this->repository->findAll($filters, $perPage);

        return array_map(fn ($offer) => OfferDTO::fromEntity($offer), $offers);
    }
}

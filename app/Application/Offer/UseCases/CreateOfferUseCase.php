<?php

declare(strict_types=1);

namespace App\Application\Offer\UseCases;

use App\Domain\Offer\Entities\Offer;
use App\Domain\Offer\Repositories\OfferRepositoryInterface;
use App\Application\Offer\Commands\CreateOfferCommand;
use App\Application\Offer\DTOs\OfferDTO;
use App\Shared\Exceptions\MaxActiveOffersException;

/**
 * Caso de uso: Publicar una nueva oferta.
 * 
 * Regla de negocio: máximo 5 ofertas activas por usuario.
 */
final class CreateOfferUseCase
{
    private const MAX_ACTIVE_OFFERS = 5;

    public function __construct(
        private readonly OfferRepositoryInterface $repository,
    ) {}

    public function execute(CreateOfferCommand $command): OfferDTO
    {
        // Validar regla de negocio
        $activeCount = $this->repository->countActiveByUser($command->userId);

        if ($activeCount >= self::MAX_ACTIVE_OFFERS) {
            throw new MaxActiveOffersException(
                "Límite de " . self::MAX_ACTIVE_OFFERS . " ofertas activas alcanzado."
            );
        }

        $offer = Offer::publish(
            userId:      $command->userId,
            type:        $command->type,
            price:       $command->price,
            amount:      $command->amount,
            contactInfo: $command->contactInfo,
            notes:       $command->notes,
        );

        $saved = $this->repository->save($offer);

        return OfferDTO::fromEntity($saved);
    }
}

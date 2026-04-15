<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controllers;

use App\Application\Offer\UseCases\CreateOfferUseCase;
use App\Application\Offer\UseCases\ListOffersUseCase;
use App\Application\Offer\Commands\CreateOfferCommand;
use App\Infrastructure\Http\Requests\CreateOfferRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OfferController extends BaseController
{
    public function __construct(
        private readonly CreateOfferUseCase $createUseCase,
        private readonly ListOffersUseCase $listUseCase,
    ) {}

    /**
     * GET /api/offers
     * Lista ofertas activas con filtros opcionales: type, user_id
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['type', 'status', 'user_id']);
        $perPage = (int) $request->get('per_page', 15);

        $offers = $this->listUseCase->execute($filters, $perPage);

        return $this->successResponse(
            data: array_map(fn ($o) => (array) $o, $offers),
        );
    }

    /**
     * POST /api/offers
     * Publica una nueva oferta de compra/venta.
     */
    public function store(CreateOfferRequest $request): JsonResponse
    {
        $command = new CreateOfferCommand(
            userId:      $request->user()->id,
            type:        $request->validated('type'),
            price:       (float) $request->validated('price'),
            amount:      (float) $request->validated('amount'),
            contactInfo: $request->validated('contact_info'),
            notes:       $request->validated('notes'),
        );

        $offer = $this->createUseCase->execute($command);

        return $this->createdResponse(
            data:    (array) $offer,
            message: 'Oferta publicada exitosamente.',
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controllers;

use App\Application\ExchangeRate\UseCases\GetExchangeRatesUseCase;
use App\Application\ExchangeRate\UseCases\ConvertCurrencyUseCase;
use App\Infrastructure\Http\Requests\ConvertCurrencyRequest;
use Illuminate\Http\JsonResponse;

/**
 * Controller delgado: solo recibe HTTP, delega a casos de uso.
 * Sin lógica de negocio aquí (SRP).
 */
class ExchangeRateController extends BaseController
{
    public function __construct(
        private readonly GetExchangeRatesUseCase $getRatesUseCase,
        private readonly ConvertCurrencyUseCase $convertUseCase,
    ) {}

    /**
     * GET /api/exchange-rates
     * Retorna todos los tipos de cambio disponibles.
     */
    public function index(): JsonResponse
    {
        $rates = $this->getRatesUseCase->execute();

        return $this->successResponse(
            data: array_map(fn ($r) => (array) $r, $rates),
            message: 'Tipos de cambio obtenidos correctamente.',
        );
    }

    /**
     * POST /api/exchange-rates/convert
     * Convierte un monto entre BOB y USD.
     */
    public function convert(ConvertCurrencyRequest $request): JsonResponse
    {
        $result = $this->convertUseCase->execute(
            amount:   (float) $request->validated('amount'),
            from:     $request->validated('from'),
            rateType: $request->validated('rate_type'),
        );

        return $this->successResponse(data: (array) $result);
    }
}

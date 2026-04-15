<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controllers\Web;

use App\Application\ExchangeRate\UseCases\GetExchangeRatesUseCase;
use App\Application\Offer\UseCases\ListOffersUseCase;
use App\Infrastructure\Http\Controllers\BaseController;
use Illuminate\View\View;

class DashboardController extends BaseController
{
    public function __construct(
        private readonly ListOffersUseCase $listOffers,
        private readonly GetExchangeRatesUseCase $getRates,
    ) {}

    public function index(): View
    {
        $myOffers = $this->listOffers->execute([
            'user_id' => auth()->id(),
            'status'  => null, // todas, no solo activas
        ]);

        $rates = $this->getRates->execute();

        $paraleloRate = collect($rates)->firstWhere('type', 'paralelo');
        $oficialRate  = collect($rates)->firstWhere('type', 'oficial');

        $activeOffersCount = collect($myOffers)->where('status', 'activa')->count();
        $totalOffersCount  = count($myOffers);

        return view('dashboard.index', [
            'myOffers' => collect($myOffers),
            'rates' => $rates,
            'paraleloRate' => $paraleloRate,
            'oficialRate' => $oficialRate,
            'activeOffersCount' => $activeOffersCount,
            'totalOffersCount' => $totalOffersCount,
        ]);
    }
}

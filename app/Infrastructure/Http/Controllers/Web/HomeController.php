<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controllers\Web;

use App\Application\ExchangeRate\UseCases\GetExchangeRatesUseCase;
use App\Application\Offer\UseCases\ListOffersUseCase;
use App\Infrastructure\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends BaseController
{
    public function __construct(
        private readonly ListOffersUseCase $listOffers,
        private readonly GetExchangeRatesUseCase $getRates,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['type']);
        $sort    = $request->get('sort', 'recent');

        $allOffers = $this->listOffers->execute($filters, 100);

        // Ordenar
        usort($allOffers, match ($sort) {
            'price_asc'  => fn ($a, $b) => $a->price <=> $b->price,
            'price_desc' => fn ($a, $b) => $b->price <=> $a->price,
            default      => fn ($a, $b) => strtotime($b->createdAt) <=> strtotime($a->createdAt),
        });

        // Home muestra solo las primeras 12; el link "ver todas" lleva a offers.index
        $hasMore    = count($allOffers) > 12;
        $offers     = collect(array_slice($allOffers, 0, 12));
        $rates      = $this->getRates->execute();
        $totalCount = count($allOffers);

        return view('home', compact('offers', 'rates', 'totalCount', 'hasMore'));
    }
}

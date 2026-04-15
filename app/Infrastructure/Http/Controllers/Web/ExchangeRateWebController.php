<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controllers\Web;

use App\Application\ExchangeRate\UseCases\GetExchangeRatesUseCase;
use App\Infrastructure\Http\Controllers\BaseController;
use Illuminate\View\View;

class ExchangeRateWebController extends BaseController
{
    public function __construct(
        private readonly GetExchangeRatesUseCase $useCase,
    ) {}

    public function index(): View
    {
        $rates = $this->useCase->execute();
        return view('exchange-rates.index', compact('rates'));
    }
}

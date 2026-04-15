<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controllers\Web;

use App\Application\Offer\Commands\CreateOfferCommand;
use App\Application\Offer\UseCases\CreateOfferUseCase;
use App\Application\Offer\UseCases\ListOffersUseCase;
use App\Application\ExchangeRate\UseCases\GetExchangeRatesUseCase;
use App\Domain\Offer\Repositories\OfferRepositoryInterface;
use App\Infrastructure\Http\Controllers\BaseController;
use App\Infrastructure\Http\Requests\CreateOfferRequest;
use App\Shared\Exceptions\MaxActiveOffersException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OfferWebController extends BaseController
{
    public function __construct(
        private readonly ListOffersUseCase $listUseCase,
        private readonly CreateOfferUseCase $createUseCase,
        private readonly GetExchangeRatesUseCase $getRatesUseCase,
        private readonly OfferRepositoryInterface $offerRepository,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['type', 'status']);
        $sort    = $request->get('sort', 'recent');

        $offers = $this->listUseCase->execute($filters, 50);
        $rates  = $this->getRatesUseCase->execute();

        // Ordenar en PHP (sin tocar el repositorio para no romper SOLID)
        usort($offers, match ($sort) {
            'price_asc'  => fn ($a, $b) => $a->price <=> $b->price,
            'price_desc' => fn ($a, $b) => $b->price <=> $a->price,
            default      => fn ($a, $b) => strtotime($b->createdAt) <=> strtotime($a->createdAt),
        });

        return view('offers.index', [
            'offers'     => collect($offers),
            'rates'      => $rates,
            'totalCount' => count($offers),
        ]);
    }

    public function create(): View
    {
        $rates       = $this->getRatesUseCase->execute();
        $activeCount = $this->offerRepository->countActiveByUser(auth()->id());

        return view('offers.create', compact('rates', 'activeCount'));
    }

    public function store(CreateOfferRequest $request): RedirectResponse
    {
        try {
            $this->createUseCase->execute(new CreateOfferCommand(
                userId:      auth()->id(),
                type:        $request->validated('type'),
                price:       (float) $request->validated('price'),
                amount:      (float) $request->validated('amount'),
                contactInfo: $request->validated('contact_info'),
                notes:       $request->validated('notes'),
            ));

            return redirect()->route('dashboard')
                ->with('success', 'Oferta publicada exitosamente. Ya está visible en el mercado.');

        } catch (MaxActiveOffersException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function close(int $id): RedirectResponse
    {
        $offer = $this->offerRepository->findById($id);

        if (!$offer || $offer->userId() !== auth()->id()) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permiso para cerrar esta oferta.');
        }

        $offer->close();
        $this->offerRepository->save($offer);

        return redirect()->route('dashboard')
            ->with('success', 'Oferta cerrada correctamente.');
    }
}

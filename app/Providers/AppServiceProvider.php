<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Interfaces de dominio
use App\Domain\ExchangeRate\Repositories\ExchangeRateRepositoryInterface;
use App\Domain\Offer\Repositories\OfferRepositoryInterface;
use App\Domain\Message\Repositories\MessageRepositoryInterface;

// Implementaciones de infraestructura
use App\Infrastructure\Repositories\EloquentExchangeRateRepository;
use App\Infrastructure\Repositories\EloquentOfferRepository;
use App\Infrastructure\Repositories\EloquentMessageRepository;

/**
 * Binding de interfaces a implementaciones concretas.
 * DIP: el dominio y la aplicación NUNCA importan Eloquent directamente.
 */
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repositorios
        $this->app->bind(
            ExchangeRateRepositoryInterface::class,
            EloquentExchangeRateRepository::class,
        );

        $this->app->bind(
            OfferRepositoryInterface::class,
            EloquentOfferRepository::class,
        );

        $this->app->bind(
            MessageRepositoryInterface::class,
            EloquentMessageRepository::class,
        );
    }

    public function boot(): void
    {
        //
    }
}

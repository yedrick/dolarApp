<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Infrastructure\Models\OfferModel;
use Illuminate\Console\Command;

/**
 * Comando programado: marca como 'expirada' toda oferta activa con más de 72 horas.
 * Ejecutar: php artisan offers:expire
 * Programar: Schedule::command('offers:expire')->dailyAt('00:00');
 */
class ExpireOffersCommand extends Command
{
    protected $signature   = 'offers:expire';
    protected $description = 'Marca como expiradas las ofertas activas con más de 72 horas';

    public function handle(): int
    {
        $threshold = now()->subHours(72);

        $expired = OfferModel::where('status', 'activa')
            ->where('created_at', '<', $threshold)
            ->update(['status' => 'expirada']);

        $this->info("✅ {$expired} oferta(s) marcadas como expiradas.");

        return self::SUCCESS;
    }
}

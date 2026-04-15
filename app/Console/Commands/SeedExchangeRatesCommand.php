<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Application\ExchangeRate\UseCases\FetchExchangeRatesFromApiUseCase;
use App\Domain\ExchangeRate\Entities\ExchangeRate;
use App\Domain\ExchangeRate\Repositories\ExchangeRateRepositoryInterface;
use App\Domain\ExchangeRate\ValueObjects\ExchangeRateType;
use Illuminate\Console\Command;

/**
 * Comando para actualizar tipos de cambio.
 * Uso: php artisan rates:seed
 * Uso desde API: php artisan rates:seed --api
 * Uso interactivo: php artisan rates:seed --interactive
 */
class SeedExchangeRatesCommand extends Command
{
    protected $signature   = 'rates:seed {--api : Obtener tipos de cambio desde API externa} {--interactive : Pedir valores manualmente}';
    protected $description = 'Actualiza o inserta los tipos de cambio';

    public function __construct(
        private readonly ExchangeRateRepositoryInterface $repository,
        private readonly ?FetchExchangeRatesFromApiUseCase $fetchUseCase = null,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        // Si se usa --api, obtener desde DolarApi
        if ($this->option('api')) {
            return $this->fetchFromApi();
        }

        // otherwise use manual/default values
        return $this->seedManually();
    }

    private function fetchFromApi(): int
    {
        if (!$this->fetchUseCase) {
            $this->error('El caso de uso de API no está disponible.');
            return self::FAILURE;
        }

        $this->info('ðŸ¤·ï¸ Obteniendo tipos de cambio desde DolarApi...');

        try {
            $results = $this->fetchUseCase->execute();

            foreach ($results as $type => $data) {
                if ($data['compra'] > 0 && $data['venta'] > 0) {
                    $this->line("  âœ± {$type}: compra {$data['compra']} / venta {$data['venta']} ({$data['fuente']})");
                } else {
                    $this->warn("  â­¤ï¸ {$type}: No se pudo obtener (manual)");
                }
            }

            $this->info('â­¤ï¸ Tipos de cambio actualizados desde API.');
            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error al obtener datos de la API: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    private function seedManually(): int
    {
        $defaults = [
            ['type' => 'oficial',       'buy' => 6.86, 'sell' => 6.96, 'source' => 'BCB'],
            ['type' => 'paralelo',      'buy' => 6.90, 'sell' => 6.97, 'source' => 'mercado'],
            ['type' => 'librecambista', 'buy' => 6.85, 'sell' => 7.00, 'source' => 'casas de cambio'],
        ];

        foreach ($defaults as $data) {
            if ($this->option('interactive')) {
                $data['buy']  = (float) $this->ask("Precio compra {$data['type']}", $data['buy']);
                $data['sell'] = (float) $this->ask("Precio venta {$data['type']}", $data['sell']);
            }

            $rate = ExchangeRate::create(
                type:      ExchangeRateType::from($data['type']),
                buyPrice:  $data['buy'],
                sellPrice: $data['sell'],
                source:    $data['source'],
            );

            $this->repository->updateOrCreateByType($rate);
            $this->line("  âœ± {$data['type']}: compra {$data['buy']} / venta {$data['sell']}");
        }

        $this->info('â­¤ï¸ Tipos de cambio actualizados.');

        return self::SUCCESS;
    }
}

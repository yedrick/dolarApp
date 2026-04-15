<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Application\ExchangeRate\UseCases\FetchExchangeRatesFromApiUseCase;
use Illuminate\Console\Command;

/**
 * Comando para obtener tipos de cambio desde API externa.
 * Uso: php artisan rates:fetch
 * Programación: Se puede agregar al scheduler para ejecución automática.
 */
class FetchExchangeRatesCommand extends Command
{
    protected $signature = 'rates:fetch';
    protected $description = 'Obtiene tipos de cambio desde API externa (DolarApi)';

    public function __construct(
        private readonly FetchExchangeRatesFromApiUseCase $fetchUseCase,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Obteniendo tipos de cambio desde DolarApi...');

        try {
            $results = $this->fetchUseCase->execute();

            $hasData = false;
            foreach ($results as $type => $data) {
                if ($data['compra'] > 0 && $data['venta'] > 0) {
                    $this->line("  - {$type}: compra {$data['compra']} / venta {$data['venta']} ({$data['fuente']})");
                    $hasData = true;
                } else {
                    $this->warn("  - {$type}: No disponible");
                }
            }

            if ($hasData) {
                $this->info('Tipos de cambio actualizados correctamente.');
                return self::SUCCESS;
            } else {
                $this->warn('No se pudo obtener ningún tipo de cambio.');
                return self::FAILURE;
            }

        } catch (\Exception $e) {
            $this->error('Error al obtener datos de la API: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}

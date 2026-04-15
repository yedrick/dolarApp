<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Infrastructure\Models\ExchangeRateModel;
use Illuminate\Database\Seeder;

class ExchangeRateSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            [
                'type'       => 'oficial',
                'buy_price'  => 6.86,
                'sell_price' => 6.96,
                'source'     => 'BCB (Banco Central de Bolivia)',
            ],
            [
                'type'       => 'paralelo',
                'buy_price'  => 6.90,
                'sell_price' => 6.97,
                'source'     => 'Mercado informal',
            ],
            [
                'type'       => 'librecambista',
                'buy_price'  => 6.85,
                'sell_price' => 7.00,
                'source'     => 'Casas de cambio',
            ],
        ];

        foreach ($rates as $rate) {
            ExchangeRateModel::updateOrCreate(
                ['type' => $rate['type']],
                $rate,
            );
        }

        $this->command->info('✅ Tipos de cambio iniciales insertados.');
    }
}

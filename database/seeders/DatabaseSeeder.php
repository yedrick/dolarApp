<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Infrastructure\Models\ExchangeRateModel;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tipos de cambio
        $this->call(ExchangeRateSeeder::class);

        // 2. Usuarios de prueba
        $users = [
            ['name' => 'Admin DólarApp', 'email' => 'admin@dolarapp.bo',  'password' => \Illuminate\Support\Facades\Hash::make('password')],
            ['name' => 'Carlos Mendoza',  'email' => 'carlos@example.com', 'password' => \Illuminate\Support\Facades\Hash::make('password')],
            ['name' => 'María Flores',    'email' => 'maria@example.com',  'password' => \Illuminate\Support\Facades\Hash::make('password')],
            ['name' => 'Juan Quispe',     'email' => 'juan@example.com',   'password' => \Illuminate\Support\Facades\Hash::make('password')],
        ];

        $createdUsers = [];
        foreach ($users as $u) {
            $createdUsers[] = \App\Models\User::firstOrCreate(['email' => $u['email']], $u);
        }

        // 3. Ofertas de prueba
        $seedOffers = [
            ['user_id' => $createdUsers[1]->id, 'type' => 'venta',  'price' => 6.97, 'amount' => 500.0,  'contact_info' => '77001122', 'notes' => 'Efectivo, zona Norte La Paz. Disponible lunes a sábado.'],
            ['user_id' => $createdUsers[1]->id, 'type' => 'venta',  'price' => 6.95, 'amount' => 1000.0, 'contact_info' => '77001122', 'notes' => 'Transferencia o efectivo.'],
            ['user_id' => $createdUsers[2]->id, 'type' => 'venta',  'price' => 7.00, 'amount' => 200.0,  'contact_info' => '71234567', 'notes' => 'Disponible esta semana, Zona Sur.'],
            ['user_id' => $createdUsers[3]->id, 'type' => 'venta',  'price' => 6.98, 'amount' => 750.0,  'contact_info' => '79876543', 'notes' => null],
            ['user_id' => $createdUsers[2]->id, 'type' => 'compra', 'price' => 6.90, 'amount' => 300.0,  'contact_info' => '71234567', 'notes' => 'Compro hoy, rápido. Zona Centro.'],
            ['user_id' => $createdUsers[3]->id, 'type' => 'compra', 'price' => 6.88, 'amount' => 100.0,  'contact_info' => '79876543', 'notes' => null],
            ['user_id' => $createdUsers[0]->id, 'type' => 'compra', 'price' => 6.92, 'amount' => 2000.0, 'contact_info' => '78000000', 'notes' => 'Compro cantidad mayor, precio negociable.'],
        ];

        foreach ($seedOffers as $o) {
            \App\Infrastructure\Models\OfferModel::create(array_merge($o, ['status' => 'activa']));
        }

        $this->command->info('Seeder OK: 4 usuarios, 7 ofertas. Login: admin@dolarapp.bo / password');
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Infrastructure\Models\OfferModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OfferModel>
 */
class OfferModelFactory extends Factory
{
    protected $model = OfferModel::class;

    public function definition(): array
    {
        return [
            'user_id'      => User::factory(),
            'type'         => fake()->randomElement(['compra', 'venta']),
            'price'        => fake()->randomFloat(2, 6.80, 7.10),
            'amount'       => fake()->randomFloat(2, 50, 5000),
            'status'       => 'activa',
            'contact_info' => fake('es_BO')->phoneNumber(),
            'notes'        => fake()->optional()->sentence(),
        ];
    }

    public function venta(): static
    {
        return $this->state(['type' => 'venta']);
    }

    public function compra(): static
    {
        return $this->state(['type' => 'compra']);
    }

    public function cerrada(): static
    {
        return $this->state(['status' => 'cerrada']);
    }

    public function sinContacto(): static
    {
        return $this->state(['contact_info' => null]);
    }
}

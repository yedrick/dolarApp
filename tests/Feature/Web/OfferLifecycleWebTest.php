<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Infrastructure\Models\ExchangeRateModel;
use App\Infrastructure\Models\OfferModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pruebas del ciclo de vida de una oferta desde la interfaz web.
 */
class OfferLifecycleWebTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        ExchangeRateModel::create(['type' => 'paralelo', 'buy_price' => 6.90, 'sell_price' => 6.97, 'source' => 'test']);
    }

    public function test_usuario_puede_cerrar_su_propia_oferta(): void
    {
        $user  = User::factory()->create();
        $offer = OfferModel::create([
            'user_id' => $user->id,
            'type'    => 'venta',
            'price'   => 6.97,
            'amount'  => 200,
            'status'  => 'activa',
        ]);

        $this->actingAs($user)
            ->patch(route('offers.close', $offer->id))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('offers', [
            'id'     => $offer->id,
            'status' => 'cerrada',
        ]);
    }

    public function test_usuario_no_puede_cerrar_oferta_ajena(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $offer = OfferModel::create([
            'user_id' => $owner->id,
            'type'    => 'venta',
            'price'   => 6.97,
            'amount'  => 100,
            'status'  => 'activa',
        ]);

        $this->actingAs($other)
            ->patch(route('offers.close', $offer->id))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        // La oferta debe seguir activa
        $this->assertDatabaseHas('offers', [
            'id'     => $offer->id,
            'status' => 'activa',
        ]);
    }

    public function test_oferta_activa_visible_en_mercado_publico(): void
    {
        $user = User::factory()->create();

        OfferModel::create([
            'user_id'      => $user->id,
            'type'         => 'compra',
            'price'        => 6.90,
            'amount'       => 1000,
            'status'       => 'activa',
            'contact_info' => '77991100',
            'notes'        => 'Zona Sur disponible',
        ]);

        $this->get(route('offers.index'))
            ->assertStatus(200)
            ->assertSee('compra', false)
            ->assertSee('6.90');
    }

    public function test_oferta_cerrada_no_visible_en_mercado(): void
    {
        $user = User::factory()->create();

        $offer = OfferModel::create([
            'user_id' => $user->id,
            'type'    => 'venta',
            'price'   => 7.00,
            'amount'  => 500,
            'status'  => 'cerrada',
        ]);

        // El listado público por defecto solo muestra activas
        $response = $this->get(route('offers.index'));
        $response->assertStatus(200);

        // 7.00 en formato con decimales no debe aparecer como oferta activa
        $offers = collect($response->viewData('offers'));
        $cerradas = $offers->where('status', 'cerrada');
        $this->assertEmpty($cerradas);
    }

    public function test_limite_de_ofertas_activas_redirige_con_error(): void
    {
        $user = User::factory()->create();

        // Crear 5 ofertas activas
        for ($i = 0; $i < 5; $i++) {
            OfferModel::create([
                'user_id' => $user->id,
                'type'    => 'venta',
                'price'   => 6.90 + ($i * 0.01),
                'amount'  => 100,
                'status'  => 'activa',
            ]);
        }

        // La 6ta debe fallar
        $this->actingAs($user)
            ->post(route('offers.store'), [
                'type'   => 'venta',
                'price'  => '6.97',
                'amount' => '100',
            ])
            ->assertRedirect()
            ->assertSessionHas('error');
    }
}

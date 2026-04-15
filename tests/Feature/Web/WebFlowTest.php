<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Infrastructure\Models\ExchangeRateModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pruebas de integración para las vistas web (Blade).
 *
 * Verifica que las páginas cargan correctamente, el flujo de
 * autenticación con sesión funciona y los formularios procesan bien.
 */
class WebFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRates();
    }

    // ── Páginas públicas ──────────────────────────────────────────────────

    public function test_home_carga_correctamente(): void
    {
        $this->get(route('home'))
            ->assertStatus(200)
            ->assertSee('DólarApp')
            ->assertSee('Tipos de cambio');
    }

    public function test_mercado_de_ofertas_es_publico(): void
    {
        $this->get(route('offers.index'))
            ->assertStatus(200)
            ->assertSee('Mercado de ofertas');
    }

    public function test_tipos_de_cambio_son_publicos(): void
    {
        $this->get(route('exchange-rates.index'))
            ->assertStatus(200)
            ->assertSee('Tipos de cambio')
            ->assertSee('oficial')
            ->assertSee('paralelo');
    }

    public function test_login_carga_correctamente(): void
    {
        $this->get(route('login'))
            ->assertStatus(200)
            ->assertSee('Ingresar');
    }

    public function test_register_carga_correctamente(): void
    {
        $this->get(route('register'))
            ->assertStatus(200)
            ->assertSee('Crear cuenta');
    }

    // ── Autenticación ─────────────────────────────────────────────────────

    public function test_usuario_puede_registrarse_via_web(): void
    {
        $this->post(route('register'), [
            'name'                  => 'Carlos Mamani',
            'email'                 => 'carlos@test.com',
            'password'              => 'Pass1234!',
            'password_confirmation' => 'Pass1234!',
        ])
        ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', ['email' => 'carlos@test.com']);
        $this->assertAuthenticated();
    }

    public function test_registro_falla_con_password_corto(): void
    {
        $this->post(route('register'), [
            'name'                  => 'Test',
            'email'                 => 'test@test.com',
            'password'              => '123',
            'password_confirmation' => '123',
        ])
        ->assertSessionHasErrors('password');
    }

    public function test_usuario_puede_hacer_login_via_web(): void
    {
        $user = User::factory()->create(['password' => bcrypt('Pass1234!')]);

        $this->post(route('login'), [
            'email'    => $user->email,
            'password' => 'Pass1234!',
        ])
        ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_falla_con_credenciales_incorrectas(): void
    {
        User::factory()->create(['email' => 'real@test.com', 'password' => bcrypt('correcto')]);

        $this->post(route('login'), [
            'email'    => 'real@test.com',
            'password' => 'incorrecto',
        ])
        ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_usuario_puede_cerrar_sesion(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('offers.index'));

        $this->assertGuest();
    }

    // ── Páginas protegidas ────────────────────────────────────────────────

    public function test_dashboard_redirige_si_no_autenticado(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_dashboard_carga_para_usuario_autenticado(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertStatus(200)
            ->assertSee('Hola')
            ->assertSee($user->name);
    }

    public function test_crear_oferta_redirige_si_no_autenticado(): void
    {
        $this->get(route('offers.create'))
            ->assertRedirect(route('login'));
    }

    public function test_formulario_crear_oferta_carga_autenticado(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('offers.create'))
            ->assertStatus(200)
            ->assertSee('Publicar oferta')
            ->assertSee('Tipo de operación');
    }

    // ── Creación de oferta vía formulario web ──────────────────────────────

    public function test_usuario_puede_publicar_oferta_via_formulario(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('offers.store'), [
                'type'         => 'venta',
                'price'        => '6.97',
                'amount'       => '500',
                'contact_info' => '77001122',
                'notes'        => 'Zona Norte La Paz',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('offers', [
            'user_id' => $user->id,
            'type'    => 'venta',
            'status'  => 'activa',
        ]);
    }

    public function test_formulario_oferta_rechaza_precio_fuera_de_rango(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('offers.store'), [
                'type'   => 'venta',
                'price'  => '150',
                'amount' => '100',
            ])
            ->assertSessionHasErrors('price');
    }

    public function test_formulario_oferta_rechaza_tipo_invalido(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('offers.store'), [
                'type'   => 'intercambio',
                'price'  => '6.97',
                'amount' => '100',
            ])
            ->assertSessionHasErrors('type');
    }

    // ── Perfil ─────────────────────────────────────────────────────────────

    public function test_perfil_carga_para_usuario_autenticado(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile.index'))
            ->assertStatus(200)
            ->assertSee($user->name)
            ->assertSee($user->email);
    }

    public function test_usuario_puede_cambiar_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('OldPass1!')]);

        $this->actingAs($user)
            ->put(route('profile.password'), [
                'current_password'      => 'OldPass1!',
                'new_password'          => 'NewPass2!',
                'new_password_confirmation' => 'NewPass2!',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');
    }

    public function test_cambio_password_falla_si_actual_incorrecta(): void
    {
        $user = User::factory()->create(['password' => bcrypt('RealPass1!')]);

        $this->actingAs($user)
            ->put(route('profile.password'), [
                'current_password'          => 'WrongPass!',
                'new_password'              => 'NewPass2!',
                'new_password_confirmation' => 'NewPass2!',
            ])
            ->assertSessionHasErrors('current_password');
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function seedRates(): void
    {
        foreach ([
            ['type' => 'oficial',       'buy_price' => 6.86, 'sell_price' => 6.96, 'source' => 'BCB'],
            ['type' => 'paralelo',      'buy_price' => 6.90, 'sell_price' => 6.97, 'source' => 'mercado'],
            ['type' => 'librecambista', 'buy_price' => 6.85, 'sell_price' => 7.00, 'source' => 'casas'],
        ] as $rate) {
            ExchangeRateModel::create($rate);
        }
    }
}

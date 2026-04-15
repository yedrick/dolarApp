<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Infrastructure\Models\ExchangeRateModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Prueba de Integración: flujo completo de la API.
 *
 * Flujo cubierto:
 *   1. Registro de usuario
 *   2. Login
 *   3. Consulta de tipos de cambio
 *   4. Conversión de moneda
 *   5. Publicación de oferta
 *   6. Visualización de oferta por otro usuario
 */
class FullFlowIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedExchangeRates();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 1. REGISTRO
    // ──────────────────────────────────────────────────────────────────────────

    public function test_usuario_puede_registrarse(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'name'                  => 'Juan Pérez',
            'email'                 => 'juan@example.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success', 'message',
                'data' => ['user' => ['id', 'name', 'email'], 'token'],
            ])
            ->assertJsonPath('data.user.email', 'juan@example.com');
    }

    public function test_registro_falla_con_email_duplicado(): void
    {
        $payload = [
            'name'                  => 'Ana',
            'email'                 => 'ana@test.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $this->postJson('/api/v1/register', $payload)->assertStatus(201);
        $this->postJson('/api/v1/register', $payload)->assertStatus(422);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 2. LOGIN
    // ──────────────────────────────────────────────────────────────────────────

    public function test_usuario_puede_hacer_login(): void
    {
        $this->registrarUsuario('maria@test.com', 'Secret123!');

        $response = $this->postJson('/api/v1/login', [
            'email'    => 'maria@test.com',
            'password' => 'Secret123!',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['token']]);
    }

    public function test_login_falla_con_credenciales_incorrectas(): void
    {
        $this->registrarUsuario('pedro@test.com', 'Correcto123!');

        $this->postJson('/api/v1/login', [
            'email'    => 'pedro@test.com',
            'password' => 'Incorrecto!',
        ])->assertStatus(401);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 3. TIPOS DE CAMBIO (público)
    // ──────────────────────────────────────────────────────────────────────────

    public function test_cualquier_usuario_puede_consultar_tipos_de_cambio(): void
    {
        $response = $this->getJson('/api/v1/exchange-rates');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(3, 'data');

        // Verificar estructura de cada tipo
        $response->assertJsonStructure([
            'data' => [['type', 'buy_price', 'sell_price', 'spread', 'source']],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 4. CONVERSIÓN
    // ──────────────────────────────────────────────────────────────────────────

    public function test_convierte_bob_a_usd_via_api(): void
    {
        $response = $this->postJson('/api/v1/exchange-rates/convert', [
            'amount'    => 690.0,
            'from'      => 'BOB',
            'rate_type' => 'paralelo',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.direction', 'BOB → USD')
            ->assertJsonPath('data.input', 690.0);

        // 690 / 6.90 = 100.00 USD
        $this->assertEquals(100.0, $response->json('data.output'));
    }

    public function test_conversion_falla_con_moneda_invalida(): void
    {
        $this->postJson('/api/v1/exchange-rates/convert', [
            'amount'    => 100.0,
            'from'      => 'EUR',
            'rate_type' => 'oficial',
        ])->assertStatus(422);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 5. PUBLICACIÓN DE OFERTA (requiere auth)
    // ──────────────────────────────────────────────────────────────────────────

    public function test_usuario_autenticado_puede_publicar_oferta(): void
    {
        $token = $this->registrarYObtenerToken('vendedor@test.com');

        $response = $this->withToken($token)->postJson('/api/v1/offers', [
            'type'         => 'venta',
            'price'        => 6.97,
            'amount'       => 500.0,
            'contact_info' => '77001122',
            'notes'        => 'Efectivo, zona Norte',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.type', 'venta')
            ->assertJsonPath('data.status', 'activa')
            ->assertJsonPath('data.price', 6.97);
    }

    public function test_publicar_oferta_sin_auth_retorna_401(): void
    {
        $this->postJson('/api/v1/offers', [
            'type'   => 'venta',
            'price'  => 6.97,
            'amount' => 100.0,
        ])->assertStatus(401);
    }

    public function test_publicar_oferta_con_precio_invalido_retorna_422(): void
    {
        $token = $this->registrarYObtenerToken('user2@test.com');

        $this->withToken($token)->postJson('/api/v1/offers', [
            'type'   => 'venta',
            'price'  => 200.0, // supera el máximo de 100
            'amount' => 100.0,
        ])->assertStatus(422);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 6. VISUALIZACIÓN DE OFERTA (flujo integración completo)
    // ──────────────────────────────────────────────────────────────────────────

    public function test_otro_usuario_puede_ver_la_oferta_publicada(): void
    {
        // Usuario A publica
        $tokenA = $this->registrarYObtenerToken('vendedorA@test.com');

        $this->withToken($tokenA)->postJson('/api/v1/offers', [
            'type'         => 'venta',
            'price'        => 6.95,
            'amount'       => 1000.0,
            'contact_info' => '79998888',
            'notes'        => 'Zona Sur',
        ])->assertStatus(201);

        // Usuario B (sin auth) consulta las ofertas públicas
        $response = $this->getJson('/api/v1/offers');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $offers = $response->json('data');
        $this->assertNotEmpty($offers);

        // Verificar que la oferta de A está visible
        $ofertaDeA = collect($offers)->firstWhere('price', 6.95);
        $this->assertNotNull($ofertaDeA);
        $this->assertEquals('venta', $ofertaDeA['type']);
        $this->assertEquals('activa', $ofertaDeA['status']);
    }

    public function test_filtrar_ofertas_por_tipo(): void
    {
        $token = $this->registrarYObtenerToken('multi@test.com');

        $this->withToken($token)->postJson('/api/v1/offers', [
            'type' => 'venta', 'price' => 6.97, 'amount' => 100.0,
        ]);
        $this->withToken($token)->postJson('/api/v1/offers', [
            'type' => 'compra', 'price' => 6.85, 'amount' => 50.0,
        ]);

        $response = $this->getJson('/api/v1/offers?type=venta');
        $response->assertStatus(200);

        foreach ($response->json('data') as $offer) {
            $this->assertEquals('venta', $offer['type']);
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function registrarUsuario(string $email, string $password): void
    {
        $this->postJson('/api/v1/register', [
            'name'                  => 'Test User',
            'email'                 => $email,
            'password'              => $password,
            'password_confirmation' => $password,
        ])->assertStatus(201);
    }

    private function registrarYObtenerToken(string $email, string $password = 'Pass1234!'): string
    {
        $res = $this->postJson('/api/v1/register', [
            'name'                  => 'Test',
            'email'                 => $email,
            'password'              => $password,
            'password_confirmation' => $password,
        ]);

        return $res->json('data.token');
    }

    private function seedExchangeRates(): void
    {
        foreach ([
            ['type' => 'oficial',       'buy_price' => 6.86, 'sell_price' => 6.96, 'source' => 'BCB'],
            ['type' => 'paralelo',      'buy_price' => 6.90, 'sell_price' => 6.97, 'source' => 'mercado'],
            ['type' => 'librecambista', 'buy_price' => 6.85, 'sell_price' => 7.00, 'source' => 'librecambista'],
        ] as $rate) {
            ExchangeRateModel::create($rate);
        }
    }
}

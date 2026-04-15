<?php

declare(strict_types=1);

namespace Tests\Acceptance;

use App\Infrastructure\Models\ExchangeRateModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * PRUEBAS DE ACEPTACIÓN
 *
 * Criterio de aceptación del cliente:
 *   - Un usuario puede publicar una oferta de compra/venta.
 *   - Otro usuario puede visualizar esa oferta.
 *   - El segundo usuario puede contactar al primero a través de la info de contacto.
 *   - El grupo revisor debe poder identificar errores en estos flujos.
 *
 * Redactadas en lenguaje de negocio (Gherkin-style en comentarios).
 */
class OfferMarketplaceAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        ExchangeRateModel::create([
            'type' => 'paralelo', 'buy_price' => 6.90, 'sell_price' => 6.97, 'source' => 'mercado',
        ]);
    }

    /**
     * Escenario 1: Publicar y visualizar oferta
     *
     * DADO que Carlos tiene cuenta en DólarApp
     * CUANDO publica una oferta de venta de $500 a Bs. 6.97
     * ENTONCES la oferta aparece en el listado público
     * Y otro usuario puede verla con los datos correctos.
     */
    public function test_escenario_publicar_y_visualizar_oferta(): void
    {
        // ── Paso 1: Carlos se registra ───────────────────────────────────────
        $carlosToken = $this->registrarYObtenerToken(
            name:  'Carlos Mendoza',
            email: 'carlos@test.com',
        );
        $this->assertNotEmpty($carlosToken, 'Carlos debe recibir un token al registrarse.');

        // ── Paso 2: Carlos publica una oferta de venta ───────────────────────
        $respuestaOferta = $this->withToken($carlosToken)
            ->postJson('/api/v1/offers', [
                'type'         => 'venta',
                'price'        => 6.97,
                'amount'       => 500.0,
                'contact_info' => '77001122',
                'notes'        => 'Efectivo, disponible en zona Norte de La Paz',
            ]);

        $respuestaOferta->assertStatus(201);
        $ofertaId = $respuestaOferta->json('data.id');
        $this->assertNotNull($ofertaId, 'La oferta debe tener un ID asignado.');

        // ── Paso 3: María (otro usuario) ve el listado público ───────────────
        $respuestaListado = $this->getJson('/api/v1/offers');

        $respuestaListado->assertStatus(200)
            ->assertJsonPath('success', true);

        $ofertas = collect($respuestaListado->json('data'));
        $this->assertGreaterThan(0, $ofertas->count(), 'El listado debe tener al menos una oferta.');

        // ── Paso 4: María encuentra la oferta de Carlos ──────────────────────
        $ofertaDeCarlos = $ofertas->firstWhere('id', $ofertaId);

        $this->assertNotNull($ofertaDeCarlos, 'La oferta de Carlos debe estar visible en el listado.');
        $this->assertEquals('venta',   $ofertaDeCarlos['type']);
        $this->assertEquals(6.97,      $ofertaDeCarlos['price']);
        $this->assertEquals(500.0,     $ofertaDeCarlos['amount']);
        $this->assertEquals('activa',  $ofertaDeCarlos['status']);

        // ── Paso 5: María puede ver la información de contacto ───────────────
        $this->assertEquals(
            '77001122',
            $ofertaDeCarlos['contact_info'],
            'La info de contacto debe ser visible para facilitar el contacto.',
        );
    }

    /**
     * Escenario 2: Múltiples ofertas de tipos distintos
     *
     * DADO que existen ofertas de compra y venta en el mercado
     * CUANDO un usuario filtra por tipo "compra"
     * ENTONCES solo ve ofertas de compra.
     */
    public function test_escenario_filtrar_por_tipo_de_oferta(): void
    {
        $tokenA = $this->registrarYObtenerToken('ana@test.com');
        $tokenB = $this->registrarYObtenerToken('beto@test.com');

        // Ana publica venta, Beto publica compra
        $this->withToken($tokenA)->postJson('/api/v1/offers', [
            'type' => 'venta', 'price' => 6.97, 'amount' => 200.0,
        ])->assertStatus(201);

        $this->withToken($tokenB)->postJson('/api/v1/offers', [
            'type' => 'compra', 'price' => 6.90, 'amount' => 300.0,
        ])->assertStatus(201);

        // Filtrar solo compras
        $resp = $this->getJson('/api/v1/offers?type=compra');
        $resp->assertStatus(200);

        foreach ($resp->json('data') as $oferta) {
            $this->assertEquals(
                'compra',
                $oferta['type'],
                'Al filtrar por "compra", todas las ofertas deben ser de tipo compra.',
            );
        }
    }

    /**
     * Escenario 3: Límite de ofertas activas
     *
     * DADO que un usuario ya tiene 5 ofertas activas
     * CUANDO intenta publicar una sexta
     * ENTONCES recibe un error con mensaje claro.
     */
    public function test_escenario_limite_de_ofertas_activas(): void
    {
        $token = $this->registrarYObtenerToken('maxofertas@test.com');

        // Publicar 5 ofertas (límite)
        for ($i = 1; $i <= 5; $i++) {
            $this->withToken($token)->postJson('/api/v1/offers', [
                'type' => 'venta', 'price' => 6.90 + ($i * 0.01), 'amount' => 100.0,
            ])->assertStatus(201);
        }

        // La sexta debe fallar
        $respuesta = $this->withToken($token)->postJson('/api/v1/offers', [
            'type' => 'venta', 'price' => 6.97, 'amount' => 50.0,
        ]);

        $respuesta->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertStringContainsString(
            '5',
            $respuesta->json('message'),
            'El mensaje de error debe mencionar el límite de 5 ofertas.',
        );
    }

    /**
     * Escenario 4: Convertir moneda antes de publicar oferta
     *
     * DADO que un usuario quiere saber cuántos dólares equivalen a sus bolivianos
     * CUANDO usa el conversor con el tipo "paralelo"
     * ENTONCES obtiene el resultado correcto antes de decidir publicar.
     */
    public function test_escenario_convertir_antes_de_publicar(): void
    {
        // Consultar conversión (ruta pública, sin auth)
        $resp = $this->postJson('/api/v1/exchange-rates/convert', [
            'amount'    => 690.0,
            'from'      => 'BOB',
            'rate_type' => 'paralelo',
        ]);

        $resp->assertStatus(200)
            ->assertJsonPath('data.direction', 'BOB → USD');

        $usd = $resp->json('data.output');
        $this->assertEquals(100.0, $usd, '690 BOB a 6.90 debe dar 100 USD.');

        // Con esa info, el usuario decide publicar su oferta
        $token = $this->registrarYObtenerToken('convertidor@test.com');

        $this->withToken($token)->postJson('/api/v1/offers', [
            'type'         => 'venta',
            'price'        => 6.90,
            'amount'       => $usd,
            'contact_info' => '78001234',
            'notes'        => 'Calculado con tipo paralelo',
        ])->assertStatus(201);
    }

    /**
     * Escenario 5: Validación de datos inválidos (para grupo revisor)
     *
     * DADO que un usuario intenta publicar con datos incorrectos
     * ENTONCES el sistema rechaza con errores específicos.
     */
    public function test_escenario_validacion_datos_invalidos(): void
    {
        $token = $this->registrarYObtenerToken('validator@test.com');

        // Tipo inválido
        $this->withToken($token)->postJson('/api/v1/offers', [
            'type' => 'intercambio', 'price' => 6.90, 'amount' => 100.0,
        ])->assertStatus(422)
          ->assertJsonStructure(['errors' => ['type']]);

        // Precio fuera de rango
        $this->withToken($token)->postJson('/api/v1/offers', [
            'type' => 'venta', 'price' => 0, 'amount' => 100.0,
        ])->assertStatus(422)
          ->assertJsonStructure(['errors' => ['price']]);

        // Monto fuera de rango
        $this->withToken($token)->postJson('/api/v1/offers', [
            'type' => 'venta', 'price' => 6.90, 'amount' => 200000.0,
        ])->assertStatus(422)
          ->assertJsonStructure(['errors' => ['amount']]);
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function registrarYObtenerToken(string $email, string $name = 'Usuario'): string
    {
        $resp = $this->postJson('/api/v1/register', [
            'name'                  => $name,
            'email'                 => $email,
            'password'              => 'Pass1234!',
            'password_confirmation' => 'Pass1234!',
        ]);

        return $resp->json('data.token');
    }
}

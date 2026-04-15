<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Domain\ExchangeRate\Entities\ExchangeRate;
use App\Domain\ExchangeRate\ValueObjects\ExchangeRateType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Domain\ExchangeRate\Entities\ExchangeRate
 *
 * Prueba esencial mínima: validar cálculo de conversión de moneda.
 */
class ExchangeRateConversionTest extends TestCase
{
    private ExchangeRate $rate;

    protected function setUp(): void
    {
        // Tipo de cambio paralelo: compra 6.90, venta 6.97
        $this->rate = ExchangeRate::create(
            type:      ExchangeRateType::paralelo(),
            buyPrice:  6.90,
            sellPrice: 6.97,
            source:    'test',
        );
    }

    // ── BOB → USD ─────────────────────────────────────────────────────────────

    public function test_convierte_bolivianos_a_dolares(): void
    {
        // 69.00 BOB / 6.90 = 10.00 USD
        $result = $this->rate->convertToDollars(69.00);
        $this->assertEquals(10.00, $result);
    }

    public function test_convierte_bolivianos_a_dolares_con_decimales(): void
    {
        // 100 BOB / 6.90 = 14.49 USD (redondeado a 2 decimales)
        $result = $this->rate->convertToDollars(100.00);
        $this->assertEquals(14.49, $result);
    }

    public function test_conversion_cero_bolivianos_retorna_cero(): void
    {
        $result = $this->rate->convertToDollars(0.0);
        $this->assertEquals(0.0, $result);
    }

    // ── USD → BOB ─────────────────────────────────────────────────────────────

    public function test_convierte_dolares_a_bolivianos(): void
    {
        // 10 USD × 6.97 = 69.70 BOB
        $result = $this->rate->convertToBolivianos(10.0);
        $this->assertEquals(69.70, $result);
    }

    public function test_convierte_dolares_fraccionados_a_bolivianos(): void
    {
        // 0.50 USD × 6.97 = 3.49 BOB
        $result = $this->rate->convertToBolivianos(0.50);
        $this->assertEquals(3.49, $result);
    }

    // ── Spread ────────────────────────────────────────────────────────────────

    public function test_calcula_spread_correctamente(): void
    {
        // 6.97 - 6.90 = 0.07
        $this->assertEquals(0.07, $this->rate->spread());
    }

    // ── Precio de compra cero ─────────────────────────────────────────────────

    public function test_lanza_excepcion_si_precio_compra_es_cero(): void
    {
        $rateConPrecioEnCero = ExchangeRate::create(
            type:      ExchangeRateType::oficial(),
            buyPrice:  0.0,
            sellPrice: 6.86,
            source:    'test',
        );

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessageMatches('/cero/');
        $rateConPrecioEnCero->convertToDollars(100.0);
    }

    // ── Tipo de cambio oficial ────────────────────────────────────────────────

    public function test_conversion_con_tipo_oficial(): void
    {
        $oficial = ExchangeRate::create(
            type:      ExchangeRateType::oficial(),
            buyPrice:  6.86,
            sellPrice: 6.96,
            source:    'BCB',
        );

        // 686 BOB / 6.86 = 100.00 USD
        $this->assertEquals(100.00, $oficial->convertToDollars(686.0));
    }

    // ── Getters ───────────────────────────────────────────────────────────────

    public function test_getters_retornan_valores_correctos(): void
    {
        $this->assertEquals('paralelo', $this->rate->type()->value());
        $this->assertEquals(6.90, $this->rate->buyPrice()->amount());
        $this->assertEquals(6.97, $this->rate->sellPrice()->amount());
        $this->assertEquals('test', $this->rate->source());
        $this->assertNull($this->rate->id());
    }
}

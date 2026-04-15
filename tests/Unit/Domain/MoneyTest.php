<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Domain\ExchangeRate\ValueObjects\Money;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Domain\ExchangeRate\ValueObjects\Money
 */
class MoneyTest extends TestCase
{
    // ── Creación ─────────────────────────────────────────────────────────────

    public function test_crea_money_desde_float_positivo(): void
    {
        $money = Money::fromFloat(6.97);
        $this->assertEquals(6.97, $money->amount());
    }

    public function test_redondea_a_cuatro_decimales(): void
    {
        $money = Money::fromFloat(6.97345678);
        $this->assertEquals(6.9735, $money->amount());
    }

    public function test_acepta_cero(): void
    {
        $money = Money::fromFloat(0.0);
        $this->assertTrue($money->isZero());
    }

    public function test_lanza_excepcion_con_monto_negativo(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/negativo/');
        Money::fromFloat(-5.0);
    }

    // ── Operaciones ───────────────────────────────────────────────────────────

    public function test_suma_dos_montos(): void
    {
        $a = Money::fromFloat(3.50);
        $b = Money::fromFloat(2.25);
        $this->assertEquals(5.75, $a->add($b)->amount());
    }

    public function test_resta_dos_montos(): void
    {
        $a = Money::fromFloat(10.0);
        $b = Money::fromFloat(4.0);
        $this->assertEquals(6.0, $a->subtract($b)->amount());
    }

    public function test_resta_lanza_excepcion_si_resultado_es_negativo(): void
    {
        $this->expectException(\DomainException::class);
        Money::fromFloat(3.0)->subtract(Money::fromFloat(5.0));
    }

    public function test_compara_mayor_que(): void
    {
        $a = Money::fromFloat(10.0);
        $b = Money::fromFloat(5.0);
        $this->assertTrue($a->greaterThan($b));
        $this->assertFalse($b->greaterThan($a));
    }

    public function test_igualdad_por_valor(): void
    {
        $a = Money::fromFloat(6.97);
        $b = Money::fromFloat(6.97);
        $this->assertTrue($a->equals($b));
    }

    public function test_to_string_formateado(): void
    {
        $money = Money::fromFloat(1234.5);
        $this->assertEquals('1,234.50', (string) $money);
    }
}

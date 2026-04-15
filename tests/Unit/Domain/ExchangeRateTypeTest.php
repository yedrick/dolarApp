<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Domain\ExchangeRate\ValueObjects\ExchangeRateType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Domain\ExchangeRate\ValueObjects\ExchangeRateType
 */
class ExchangeRateTypeTest extends TestCase
{
    public function test_crea_tipo_oficial(): void
    {
        $type = ExchangeRateType::from('oficial');
        $this->assertEquals('oficial', $type->value());
    }

    public function test_crea_tipo_paralelo(): void
    {
        $type = ExchangeRateType::paralelo();
        $this->assertEquals('paralelo', $type->value());
    }

    public function test_crea_tipo_librecambista(): void
    {
        $type = ExchangeRateType::librecambista();
        $this->assertEquals('librecambista', $type->value());
    }

    public function test_lanza_excepcion_con_tipo_invalido(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/inválido/');
        ExchangeRateType::from('negro');
    }

    public function test_igualdad_entre_tipos(): void
    {
        $a = ExchangeRateType::oficial();
        $b = ExchangeRateType::from('oficial');
        $this->assertTrue($a->equals($b));
    }

    public function test_desigualdad_entre_tipos_distintos(): void
    {
        $a = ExchangeRateType::oficial();
        $b = ExchangeRateType::paralelo();
        $this->assertFalse($a->equals($b));
    }

    public function test_retorna_todos_los_tipos(): void
    {
        $all = ExchangeRateType::all();
        $this->assertContains('oficial', $all);
        $this->assertContains('paralelo', $all);
        $this->assertContains('librecambista', $all);
        $this->assertCount(3, $all);
    }

    public function test_to_string(): void
    {
        $this->assertEquals('paralelo', (string) ExchangeRateType::paralelo());
    }
}

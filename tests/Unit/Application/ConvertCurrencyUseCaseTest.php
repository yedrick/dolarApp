<?php

declare(strict_types=1);

namespace Tests\Unit\Application;

use App\Application\ExchangeRate\UseCases\ConvertCurrencyUseCase;
use App\Application\ExchangeRate\DTOs\ConversionResultDTO;
use App\Domain\ExchangeRate\Entities\ExchangeRate;
use App\Domain\ExchangeRate\Repositories\ExchangeRateRepositoryInterface;
use App\Domain\ExchangeRate\ValueObjects\ExchangeRateType;
use App\Shared\Exceptions\ExchangeRateNotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Application\ExchangeRate\UseCases\ConvertCurrencyUseCase
 */
class ConvertCurrencyUseCaseTest extends TestCase
{
    private MockObject $repo;
    private ConvertCurrencyUseCase $useCase;

    protected function setUp(): void
    {
        $this->repo    = $this->createMock(ExchangeRateRepositoryInterface::class);
        $this->useCase = new ConvertCurrencyUseCase($this->repo);
    }

    public function test_convierte_bob_a_usd_exitosamente(): void
    {
        $this->repo->method('findByType')->willReturn(
            ExchangeRate::create(ExchangeRateType::paralelo(), 6.90, 6.97, 'test')
        );

        $result = $this->useCase->execute(69.0, 'BOB', 'paralelo');

        $this->assertInstanceOf(ConversionResultDTO::class, $result);
        $this->assertEquals(69.0, $result->input);
        $this->assertEquals(10.0, $result->output);
        $this->assertEquals('BOB → USD', $result->direction);
        $this->assertEquals('paralelo', $result->rateType);
    }

    public function test_convierte_usd_a_bob_exitosamente(): void
    {
        $this->repo->method('findByType')->willReturn(
            ExchangeRate::create(ExchangeRateType::paralelo(), 6.90, 6.97, 'test')
        );

        $result = $this->useCase->execute(10.0, 'USD', 'paralelo');

        $this->assertEquals(69.70, $result->output);
        $this->assertEquals('USD → BOB', $result->direction);
    }

    public function test_lanza_excepcion_si_tipo_cambio_no_existe(): void
    {
        $this->repo->method('findByType')->willReturn(null);

        $this->expectException(ExchangeRateNotFoundException::class);
        $this->useCase->execute(100.0, 'BOB', 'paralelo');
    }

    public function test_lanza_excepcion_con_moneda_no_soportada(): void
    {
        $this->repo->method('findByType')->willReturn(
            ExchangeRate::create(ExchangeRateType::oficial(), 6.86, 6.96, 'BCB')
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/no soportada/');
        $this->useCase->execute(100.0, 'EUR', 'oficial');
    }

    public function test_retorna_precios_compra_venta_en_dto(): void
    {
        $this->repo->method('findByType')->willReturn(
            ExchangeRate::create(ExchangeRateType::librecambista(), 6.85, 7.00, 'librecambista')
        );

        $result = $this->useCase->execute(100.0, 'BOB', 'librecambista');

        $this->assertEquals(6.85, $result->buyPrice);
        $this->assertEquals(7.00, $result->sellPrice);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit\Application;

use App\Application\Offer\Commands\CreateOfferCommand;
use App\Application\Offer\DTOs\OfferDTO;
use App\Application\Offer\UseCases\CreateOfferUseCase;
use App\Domain\Offer\Entities\Offer;
use App\Domain\Offer\Repositories\OfferRepositoryInterface;
use App\Shared\Exceptions\MaxActiveOffersException;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Application\Offer\UseCases\CreateOfferUseCase
 *
 * Prueba esencial mínima: validar creación de oferta (capa aplicación).
 */
class CreateOfferUseCaseTest extends TestCase
{
    private MockObject $repo;
    private CreateOfferUseCase $useCase;

    protected function setUp(): void
    {
        $this->repo    = $this->createMock(OfferRepositoryInterface::class);
        $this->useCase = new CreateOfferUseCase($this->repo);
    }

    public function test_crea_oferta_exitosamente(): void
    {
        $this->repo->method('countActiveByUser')->willReturn(0);
        $this->repo->method('save')->willReturnCallback(
            fn (Offer $offer) => Offer::reconstitute(
                id:             99,
                userId:         $offer->userId(),
                type:           $offer->type()->value(),
                price:          $offer->price()->amount(),
                amount:         $offer->amount(),
                reservedAmount: $offer->reservedAmount(),
                status:         $offer->status()->value(),
                contactInfo:    $offer->contactInfo(),
                notes:          $offer->notes(),
                createdAt:      new DateTimeImmutable(),
                updatedAt:      new DateTimeImmutable(),
            )
        );

        $command = new CreateOfferCommand(
            userId:      1,
            type:        'venta',
            price:       6.97,
            amount:      500.0,
            contactInfo: '77001122',
            notes:       'Disponible hoy',
        );

        $dto = $this->useCase->execute($command);

        $this->assertInstanceOf(OfferDTO::class, $dto);
        $this->assertEquals(99, $dto->id);
        $this->assertEquals('venta', $dto->type);
        $this->assertEquals(6.97, $dto->price);
        $this->assertEquals('activa', $dto->status);
    }

    public function test_lanza_excepcion_si_supera_limite_activas(): void
    {
        // Simular que ya tiene 5 ofertas activas
        $this->repo->method('countActiveByUser')->willReturn(5);

        $this->expectException(MaxActiveOffersException::class);
        $this->expectExceptionMessageMatches('/5/');

        $this->useCase->execute(new CreateOfferCommand(
            userId:      1,
            type:        'venta',
            price:       6.97,
            amount:      100.0,
            contactInfo: null,
            notes:       null,
        ));
    }

    public function test_repositorio_es_llamado_una_vez(): void
    {
        $this->repo->method('countActiveByUser')->willReturn(0);
        $this->repo->expects($this->once())
            ->method('save')
            ->willReturnCallback(fn (Offer $o) => Offer::reconstitute(
                1, $o->userId(), $o->type()->value(),
                $o->price()->amount(), $o->amount(), $o->reservedAmount(),
                $o->status()->value(), null, null,
                new DateTimeImmutable(), new DateTimeImmutable(),
            ));

        $this->useCase->execute(new CreateOfferCommand(1, 'compra', 6.90, 100.0, null, null));
    }

    public function test_oferta_creada_tiene_estado_activa(): void
    {
        $this->repo->method('countActiveByUser')->willReturn(2);
        $this->repo->method('save')->willReturnCallback(
            fn (Offer $o) => Offer::reconstitute(
                5, $o->userId(), $o->type()->value(),
                $o->price()->amount(), $o->amount(), $o->reservedAmount(),
                'activa', null, null,
                new DateTimeImmutable(), new DateTimeImmutable(),
            )
        );

        $dto = $this->useCase->execute(
            new CreateOfferCommand(1, 'compra', 6.85, 50.0, null, null)
        );

        $this->assertEquals('activa', $dto->status);
    }
}

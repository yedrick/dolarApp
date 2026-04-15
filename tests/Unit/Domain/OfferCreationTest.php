<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Domain\Offer\Entities\Offer;
use App\Domain\Offer\Events\OfferPublished;
use App\Domain\Offer\ValueObjects\OfferType;
use App\Domain\Offer\ValueObjects\OfferStatus;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Domain\Offer\Entities\Offer
 *
 * Prueba esencial mínima: validar creación de oferta.
 */
class OfferCreationTest extends TestCase
{
    // ── Creación exitosa ──────────────────────────────────────────────────────

    public function test_crea_oferta_de_venta_correctamente(): void
    {
        $offer = Offer::publish(
            userId:      1,
            type:        'venta',
            price:       6.97,
            amount:      500.0,
            contactInfo: '76543210',
            notes:       'Disponible hoy',
        );

        $this->assertNull($offer->id());
        $this->assertEquals(1, $offer->userId());
        $this->assertEquals('venta', $offer->type()->value());
        $this->assertEquals(6.97, $offer->price()->amount());
        $this->assertEquals(500.0, $offer->amount());
        $this->assertEquals('activa', $offer->status()->value());
        $this->assertEquals('76543210', $offer->contactInfo());
        $this->assertEquals('Disponible hoy', $offer->notes());
    }

    public function test_crea_oferta_de_compra_correctamente(): void
    {
        $offer = Offer::publish(
            userId:      2,
            type:        'compra',
            price:       6.90,
            amount:      200.0,
            contactInfo: null,
            notes:       null,
        );

        $this->assertTrue($offer->type()->isCompra());
        $this->assertTrue($offer->status()->isActive());
    }

    public function test_oferta_nueva_tiene_estado_activa(): void
    {
        $offer = $this->buildOffer();
        $this->assertEquals('activa', $offer->status()->value());
    }

    // ── Eventos de dominio ────────────────────────────────────────────────────

    public function test_publicar_oferta_emite_evento_de_dominio(): void
    {
        $offer  = $this->buildOffer();
        $events = $offer->pullDomainEvents();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(OfferPublished::class, $events[0]);
        $this->assertEquals(1, $events[0]->userId);
        $this->assertEquals('venta', $events[0]->offerType);
    }

    public function test_pull_domain_events_vacia_la_cola(): void
    {
        $offer = $this->buildOffer();
        $offer->pullDomainEvents();

        $this->assertEmpty($offer->pullDomainEvents());
    }

    // ── Cierre de oferta ──────────────────────────────────────────────────────

    public function test_cerrar_oferta_activa(): void
    {
        $offer = $this->buildOffer();
        $offer->close();
        $this->assertTrue($offer->status()->isClosed());
    }

    public function test_cerrar_oferta_ya_cerrada_lanza_excepcion(): void
    {
        $offer = $this->buildOffer();
        $offer->close();

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessageMatches('/cerrada/');
        $offer->close();
    }

    // ── Actualización de precio ───────────────────────────────────────────────

    public function test_actualizar_precio_de_oferta_activa(): void
    {
        $offer = $this->buildOffer();
        $offer->updatePrice(7.05);
        $this->assertEquals(7.05, $offer->price()->amount());
    }

    public function test_actualizar_precio_de_oferta_cerrada_lanza_excepcion(): void
    {
        $offer = $this->buildOffer();
        $offer->close();

        $this->expectException(\DomainException::class);
        $offer->updatePrice(7.05);
    }

    // ── Tipo inválido ─────────────────────────────────────────────────────────

    public function test_tipo_invalido_lanza_excepcion(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/inválido/');

        Offer::publish(
            userId:      1,
            type:        'intercambio',
            price:       6.97,
            amount:      100.0,
            contactInfo: null,
            notes:       null,
        );
    }

    // ── Precio negativo ───────────────────────────────────────────────────────

    public function test_precio_negativo_lanza_excepcion(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/negativo/');

        Offer::publish(
            userId:      1,
            type:        'venta',
            price:       -1.0,
            amount:      100.0,
            contactInfo: null,
            notes:       null,
        );
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function buildOffer(array $overrides = []): Offer
    {
        return Offer::publish(
            userId:      $overrides['userId']      ?? 1,
            type:        $overrides['type']        ?? 'venta',
            price:       $overrides['price']       ?? 6.97,
            amount:      $overrides['amount']      ?? 100.0,
            contactInfo: $overrides['contactInfo'] ?? '77001122',
            notes:       $overrides['notes']       ?? null,
        );
    }
}

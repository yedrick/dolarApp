<?php

declare(strict_types=1);

namespace App\Application\ExchangeRate\DTOs;

/**
 * DTO para mapear la respuesta de la API DolarApi.
 * SRP: Solo representa los datos de la API externa.
 */
final class DolarApiResponseDTO
{
    public function __construct(
        public readonly string $nombre,
        public readonly string $moneda,
        public readonly string $sigla,
        public readonly float $compra,
        public readonly float $venta,
        public readonly string $fechaActualizacion,
    ) {}

    /**
     * Crea el DTO desde el array de la respuesta JSON de la API.
     * @param array<string, mixed> $data
     */
    public static function fromApiResponse(array $data): self
    {
        return new self(
            nombre: $data['nombre'] ?? '',
            moneda: $data['moneda'] ?? 'USD',
            sigla: $data['sigla'] ?? '$',
            compra: (float) ($data['compra'] ?? 0),
            venta: (float) ($data['venta'] ?? 0),
            fechaActualizacion: $data['fechaActualizacion'] ?? '',
        );
    }
}

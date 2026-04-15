<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\ExchangeRate\Entities\ExchangeRate;
use App\Domain\ExchangeRate\Repositories\ExchangeRateRepositoryInterface;
use App\Domain\ExchangeRate\ValueObjects\ExchangeRateType;
use App\Infrastructure\Models\ExchangeRateModel;
use DateTimeImmutable;

/**
 * Implementación Eloquent del repositorio de tipos de cambio.
 * 
 * DIP: Implementa la interfaz del dominio.
 * SRP: Solo se ocupa de persistencia, no de lógica de negocio.
 */
final class EloquentExchangeRateRepository implements ExchangeRateRepositoryInterface
{
    public function findByType(ExchangeRateType $type): ?ExchangeRate
    {
        $model = ExchangeRateModel::where('type', $type->value())->first();

        return $model ? $this->toDomain($model) : null;
    }

    /** @return ExchangeRate[] */
    public function findAll(): array
    {
        return ExchangeRateModel::orderBy('type')
            ->get()
            ->map(fn ($m) => $this->toDomain($m))
            ->all();
    }

    public function save(ExchangeRate $rate): ExchangeRate
    {
        $model = new ExchangeRateModel();
        $model->fill($this->toArray($rate));
        $model->save();

        return $this->toDomain($model);
    }

    public function updateOrCreateByType(ExchangeRate $rate): ExchangeRate
    {
        $model = ExchangeRateModel::updateOrCreate(
            ['type' => $rate->type()->value()],
            $this->toArray($rate),
        );

        return $this->toDomain($model);
    }

    // ── Mappers ──────────────────────────────────────────────────────────────

    private function toDomain(ExchangeRateModel $model): ExchangeRate
    {
        $updatedAt = $model->updated_at instanceof \Illuminate\Support\Carbon
            ? $model->updated_at->toDateTimeImmutable()
            : new DateTimeImmutable($model->updated_at);

        return ExchangeRate::reconstitute(
            id:        $model->id,
            type:      ExchangeRateType::from($model->type),
            buyPrice:  (float) $model->buy_price,
            sellPrice: (float) $model->sell_price,
            source:    $model->source,
            updatedAt: $updatedAt,
        );
    }

    private function toArray(ExchangeRate $rate): array
    {
        return [
            'type'       => $rate->type()->value(),
            'buy_price'  => $rate->buyPrice()->amount(),
            'sell_price' => $rate->sellPrice()->amount(),
            'source'     => $rate->source(),
        ];
    }
}

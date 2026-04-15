<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Offer\Entities\Offer;
use App\Domain\Offer\Repositories\OfferRepositoryInterface;
use App\Infrastructure\Models\OfferModel;
use DateTimeImmutable;

final class EloquentOfferRepository implements OfferRepositoryInterface
{
    public function findById(int $id): ?Offer
    {
        $model = OfferModel::find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function findAll(array $filters = [], int $perPage = 15): array
    {
        $query = OfferModel::query()->with('user');

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        } else {
            // Por defecto solo activas
            $query->where('status', 'activa');
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query
            ->orderByDesc('created_at')
            ->take($perPage)
            ->get()
            ->map(fn ($m) => $this->toDomain($m))
            ->all();
    }

    public function findByUser(int $userId): array
    {
        return OfferModel::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($m) => $this->toDomain($m))
            ->all();
    }

    public function save(Offer $offer): Offer
    {
        if ($offer->id() !== null) {
            $model = OfferModel::findOrFail($offer->id());
        } else {
            $model = new OfferModel();
        }

        $model->fill([
            'user_id'         => $offer->userId(),
            'type'            => $offer->type()->value(),
            'price'           => $offer->price()->amount(),
            'amount'          => $offer->amount(),
            'reserved_amount' => $offer->reservedAmount(),
            'status'          => $offer->status()->value(),
            'contact_info'    => $offer->contactInfo(),
            'notes'           => $offer->notes(),
        ]);

        $model->save();

        return $this->toDomain($model);
    }

    public function delete(int $id): void
    {
        OfferModel::destroy($id);
    }

    public function countActiveByUser(int $userId): int
    {
        return OfferModel::where('user_id', $userId)
            ->where('status', 'activa')
            ->count();
    }

    // ── Mapper ───────────────────────────────────────────────────────────────

    private function toDomain(OfferModel $model): Offer
    {
        $createdAt = $model->created_at instanceof \Illuminate\Support\Carbon
            ? $model->created_at->toDateTimeImmutable()
            : new DateTimeImmutable($model->created_at);

        $updatedAt = $model->updated_at instanceof \Illuminate\Support\Carbon
            ? $model->updated_at->toDateTimeImmutable()
            : new DateTimeImmutable($model->updated_at);

        return Offer::reconstitute(
            id:             $model->id,
            userId:         $model->user_id,
            type:           $model->type,
            price:          (float) $model->price,
            amount:         (float) $model->amount,
            reservedAmount: (float) $model->reserved_amount,
            status:         $model->status,
            contactInfo:    $model->contact_info,
            notes:          $model->notes,
            createdAt:      $createdAt,
            updatedAt:      $updatedAt,
        );
    }
}

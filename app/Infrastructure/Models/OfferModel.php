<?php

declare(strict_types=1);

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferModel extends Model
{
    use HasFactory;

    protected $table = 'offers';

    protected $fillable = [
        'user_id',
        'type',
        'price',
        'amount',
        'status',
        'contact_info',
        'notes',
    ];

    protected $casts = [
        'price'  => 'float',
        'amount' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}

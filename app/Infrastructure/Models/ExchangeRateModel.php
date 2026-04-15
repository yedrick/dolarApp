<?php

declare(strict_types=1);

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modelo Eloquent: solo para persistencia.
 * NO contiene lógica de negocio (eso vive en Domain).
 */
class ExchangeRateModel extends Model
{
    use HasFactory;

    protected $table = 'exchange_rates';

    protected $fillable = [
        'type',
        'buy_price',
        'sell_price',
        'source',
    ];

    protected $casts = [
        'buy_price'  => 'float',
        'sell_price' => 'float',
    ];
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Domain\ExchangeRate\ValueObjects\ExchangeRateType;

class ConvertCurrencyRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'amount'    => ['required', 'numeric', 'min:0.01'],
            'from'      => ['required', 'string', 'in:BOB,USD'],
            'rate_type' => ['required', 'string', 'in:' . implode(',', ExchangeRateType::all())],
        ];
    }

    public function messages(): array
    {
        return [
            'from.in'      => 'La moneda debe ser BOB o USD.',
            'rate_type.in' => 'Tipo de cambio inválido. Use: oficial, paralelo, librecambista.',
        ];
    }
}

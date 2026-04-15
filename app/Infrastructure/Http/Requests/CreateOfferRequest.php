<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Offer\ValueObjects\OfferType;

class CreateOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'type'         => ['required', 'string', 'in:' . implode(',', [OfferType::COMPRA, OfferType::VENTA])],
            'price'        => ['required', 'numeric', 'min:1', 'max:100'],
            'amount'       => ['required', 'numeric', 'min:1', 'max:100000'],
            'contact_info' => ['nullable', 'string', 'max:200'],
            'notes'        => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.in'       => 'El tipo debe ser "compra" o "venta".',
            'price.min'     => 'El precio mínimo es 1 Bs.',
            'price.max'     => 'El precio máximo es 100 Bs.',
            'amount.min'    => 'El monto mínimo es 1 USD.',
            'amount.max'    => 'El monto máximo es 100,000 USD.',
        ];
    }
}

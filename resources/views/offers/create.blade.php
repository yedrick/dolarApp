@extends('layouts.app')
@section('title', 'Publicar oferta')

@section('content')
<div class="container container--narrow">

    <div class="page-header">
        <div>
            <h1 class="page-title">Publicar oferta</h1>
            <p class="page-subtitle">Tu oferta estará visible para todos los usuarios del mercado.</p>
        </div>
        <a href="{{ route('offers.index') }}" class="btn btn--ghost">← Volver</a>
    </div>

    {{-- Referencia de tipos de cambio --}}
    <div class="rates-bar" style="margin-bottom:1.5rem">
        @foreach($rates as $rate)
            <div class="rate-pill">
                <span class="rate-pill__label">{{ ucfirst($rate->type) }}</span>
                <span class="rate-pill__buy">C: Bs.{{ number_format($rate->buyPrice, 2) }}</span>
                <span class="rate-pill__sell">V: Bs.{{ number_format($rate->sellPrice, 2) }}</span>
            </div>
        @endforeach
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('offers.store') }}" class="form" novalidate>
            @csrf

            {{-- Tipo de oferta --}}
            <div class="form-group">
                <label class="form-label">Tipo de operación <span class="required">*</span></label>
                <div class="radio-group">
                    <label class="radio-card {{ old('type') === 'venta' || !old('type') ? 'radio-card--selected' : '' }}" id="label-venta">
                        <input type="radio" name="type" value="venta"
                               {{ old('type', 'venta') === 'venta' ? 'checked' : '' }}
                               class="radio-input" onchange="selectType('venta')">
                        <span class="radio-card__icon">↑</span>
                        <span class="radio-card__label">Vendo dólares</span>
                        <span class="radio-card__desc">Tienes USD y quieres BOB</span>
                    </label>
                    <label class="radio-card {{ old('type') === 'compra' ? 'radio-card--selected' : '' }}" id="label-compra">
                        <input type="radio" name="type" value="compra"
                               {{ old('type') === 'compra' ? 'checked' : '' }}
                               class="radio-input" onchange="selectType('compra')">
                        <span class="radio-card__icon">↓</span>
                        <span class="radio-card__label">Compro dólares</span>
                        <span class="radio-card__desc">Tienes BOB y quieres USD</span>
                    </label>
                </div>
                @error('type')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            {{-- Precio --}}
            <div class="form-group">
                <label class="form-label" for="price">
                    Precio por dólar (Bs.) <span class="required">*</span>
                    <span class="form-label__hint">Rango permitido: Bs. 1 – 100</span>
                </label>
                <div class="input-group">
                    <span class="input-group__prefix">Bs.</span>
                    <input
                        type="number"
                        id="price"
                        name="price"
                        class="form-input form-input--prefixed @error('price') form-input--error @enderror"
                        value="{{ old('price') }}"
                        step="0.01"
                        min="1"
                        max="100"
                        placeholder="6.97"
                        oninput="calcTotal()"
                    >
                </div>
                @error('price')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            {{-- Monto --}}
            <div class="form-group">
                <label class="form-label" for="amount">
                    Monto en dólares (USD) <span class="required">*</span>
                    <span class="form-label__hint">Máximo: $100,000</span>
                </label>
                <div class="input-group">
                    <span class="input-group__prefix">$</span>
                    <input
                        type="number"
                        id="amount"
                        name="amount"
                        class="form-input form-input--prefixed @error('amount') form-input--error @enderror"
                        value="{{ old('amount') }}"
                        step="0.01"
                        min="1"
                        max="100000"
                        placeholder="500"
                        oninput="calcTotal()"
                    >
                </div>
                @error('amount')
                    <span class="form-error">{{ $message }}</span>
                @enderror
                {{-- Cálculo en tiempo real --}}
                <div id="calc-total" class="calc-preview" style="display:none">
                    Total en bolivianos: <strong id="calc-bob" class="font-mono"></strong>
                </div>
            </div>

            {{-- Contacto --}}
            <div class="form-group">
                <label class="form-label" for="contact_info">
                    Número de contacto
                    <span class="form-label__hint">WhatsApp o teléfono</span>
                </label>
                <input
                    type="text"
                    id="contact_info"
                    name="contact_info"
                    class="form-input @error('contact_info') form-input--error @enderror"
                    value="{{ old('contact_info') }}"
                    placeholder="77001122"
                    maxlength="200"
                >
                @error('contact_info')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            {{-- Notas --}}
            <div class="form-group">
                <label class="form-label" for="notes">
                    Notas adicionales
                    <span class="form-label__hint">Zona, disponibilidad, etc. (opcional)</span>
                </label>
                <textarea
                    id="notes"
                    name="notes"
                    class="form-input form-textarea @error('notes') form-input--error @enderror"
                    rows="3"
                    maxlength="500"
                    placeholder="Ejemplo: Efectivo, disponible de lunes a viernes en zona Norte de La Paz"
                >{{ old('notes') }}</textarea>
                @error('notes')
                    <span class="form-error">{{ $message }}</span>
                @enderror
                <span class="form-hint" id="notes-count">0 / 500</span>
            </div>

            {{-- Aviso de límite --}}
            @if($activeCount >= 4)
                <div class="alert alert--warning">
                    Tienes {{ $activeCount }} de 5 ofertas activas permitidas.
                </div>
            @endif

            <div class="form-actions">
                <button type="submit" class="btn btn--primary btn--lg">
                    Publicar oferta
                </button>
                <a href="{{ route('offers.index') }}" class="btn btn--ghost btn--lg">Cancelar</a>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
function selectType(type) {
    document.getElementById('label-venta').classList.toggle('radio-card--selected', type === 'venta');
    document.getElementById('label-compra').classList.toggle('radio-card--selected', type === 'compra');
}

function calcTotal() {
    const price  = parseFloat(document.getElementById('price').value);
    const amount = parseFloat(document.getElementById('amount').value);
    const div    = document.getElementById('calc-total');

    if (price > 0 && amount > 0) {
        const total = (price * amount).toFixed(2);
        document.getElementById('calc-bob').textContent = 'Bs. ' + Number(total).toLocaleString('es-BO', {minimumFractionDigits: 2});
        div.style.display = 'block';
    } else {
        div.style.display = 'none';
    }
}

const notesEl = document.getElementById('notes');
const countEl = document.getElementById('notes-count');
notesEl.addEventListener('input', () => {
    countEl.textContent = notesEl.value.length + ' / 500';
});
</script>
@endpush

@extends('layouts.app')
@section('title', 'Tipos de cambio')

@section('content')
<div class="container">

    <div class="page-header">
        <div>
            <h1 class="page-title">Tipos de cambio</h1>
            <p class="page-subtitle">Actualizado: {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    {{-- Cards de tipos de cambio --}}
    <div class="rates-grid">
        @foreach($rates as $rate)
            <div class="rate-card rate-card--{{ $rate->type }}">
                <div class="rate-card__header">
                    <span class="rate-card__type">{{ ucfirst($rate->type) }}</span>
                    <span class="rate-card__source text-muted text-sm">{{ $rate->source }}</span>
                </div>
                <div class="rate-card__prices">
                    <div class="rate-price">
                        <span class="rate-price__label">Compra</span>
                        <span class="rate-price__value font-mono">Bs. {{ number_format($rate->buyPrice, 2) }}</span>
                    </div>
                    <div class="rate-price rate-price--sell">
                        <span class="rate-price__label">Venta</span>
                        <span class="rate-price__value font-mono">Bs. {{ number_format($rate->sellPrice, 2) }}</span>
                    </div>
                </div>
                <div class="rate-card__spread text-muted text-sm">
                    Spread: Bs. {{ number_format($rate->spread, 4) }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- Conversor completo --}}
    <section class="section">
        <h2 class="section-title">Conversor de divisas</h2>
        <div class="converter-card converter-card--full">
            <div class="converter-row">
                <div class="form-group" style="flex:2">
                    <label class="form-label">Monto a convertir</label>
                    <input type="number" id="conv-amount" class="form-input form-input--lg"
                           value="100" min="0.01" step="0.01" placeholder="Ingresa el monto">
                </div>
                <div class="form-group" style="flex:1">
                    <label class="form-label">Moneda origen</label>
                    <select id="conv-from" class="form-input form-select">
                        <option value="BOB">Bolivianos (Bs.)</option>
                        <option value="USD">Dólares (USD)</option>
                    </select>
                </div>
                <div class="form-group" style="flex:1">
                    <label class="form-label">Tipo de cambio</label>
                    <select id="conv-type" class="form-input form-select">
                        <option value="oficial">Oficial (BCB)</option>
                        <option value="paralelo" selected>Paralelo</option>
                        <option value="librecambista">Librecambista</option>
                    </select>
                </div>
            </div>

            <button id="conv-btn" class="btn btn--primary btn--lg" style="width:100%">
                Convertir ahora
            </button>

            <div id="conv-result" class="converter-result converter-result--lg" style="display:none">
                <div class="conv-result-row">
                    <span class="text-muted">Resultado:</span>
                    <span id="conv-output" class="converter-result__value font-mono"></span>
                </div>
                <div class="conv-result-row">
                    <span class="text-muted">Dirección:</span>
                    <span id="conv-direction" class="text-sm"></span>
                </div>
                <div class="conv-result-row">
                    <span class="text-muted">Precio compra:</span>
                    <span id="conv-buy" class="font-mono text-sm"></span>
                </div>
                <div class="conv-result-row">
                    <span class="text-muted">Precio venta:</span>
                    <span id="conv-sell" class="font-mono text-sm"></span>
                </div>
            </div>

            <div id="conv-error" class="alert alert--error" style="display:none"></div>
        </div>
    </section>

    {{-- Comparativa de tipos --}}
    <section class="section">
        <h2 class="section-title">Comparativa de precios</h2>
        <div class="offers-table-wrapper">
            <table class="offers-table">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Compra (Bs.)</th>
                        <th>Venta (Bs.)</th>
                        <th>Spread</th>
                        <th>Fuente</th>
                        <th>Actualizado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rates as $rate)
                    <tr>
                        <td><span class="badge badge--{{ $rate->type === 'oficial' ? 'active' : ($rate->type === 'paralelo' ? 'sell' : 'buy') }}">{{ ucfirst($rate->type) }}</span></td>
                        <td class="font-mono">{{ number_format($rate->buyPrice, 4) }}</td>
                        <td class="font-mono">{{ number_format($rate->sellPrice, 4) }}</td>
                        <td class="font-mono text-muted">{{ number_format($rate->spread, 4) }}</td>
                        <td class="text-sm">{{ $rate->source }}</td>
                        <td class="text-muted text-sm">{{ \Carbon\Carbon::parse($rate->updatedAt)->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

</div>
@endsection

@push('scripts')
<script>
document.getElementById('conv-btn').addEventListener('click', async () => {
    const amount = parseFloat(document.getElementById('conv-amount').value);
    const from   = document.getElementById('conv-from').value;
    const type   = document.getElementById('conv-type').value;

    const btn    = document.getElementById('conv-btn');
    const result = document.getElementById('conv-result');
    const errDiv = document.getElementById('conv-error');

    btn.textContent = 'Calculando...';
    btn.disabled    = true;
    result.style.display = 'none';
    errDiv.style.display = 'none';

    try {
        const res = await fetch('/api/v1/exchange-rates/convert', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ amount, from, rate_type: type }),
        });

        const data = await res.json();

        if (data.success) {
            const r     = data.data;
            const isBob = from === 'BOB';
            document.getElementById('conv-output').textContent    = (isBob ? '$ ' : 'Bs. ') + r.output.toFixed(2);
            document.getElementById('conv-direction').textContent = r.direction;
            document.getElementById('conv-buy').textContent       = 'Bs. ' + r.buy_price.toFixed(4);
            document.getElementById('conv-sell').textContent      = 'Bs. ' + r.sell_price.toFixed(4);
            result.style.display = 'grid';
        } else {
            errDiv.textContent      = data.message || 'Error al calcular';
            errDiv.style.display    = 'block';
        }
    } catch(e) {
        errDiv.textContent   = 'Error de conexión con el servidor.';
        errDiv.style.display = 'block';
    } finally {
        btn.textContent = 'Convertir ahora';
        btn.disabled    = false;
    }
});

// Permitir Enter en el campo de monto
document.getElementById('conv-amount').addEventListener('keypress', e => {
    if (e.key === 'Enter') document.getElementById('conv-btn').click();
});
</script>
@endpush

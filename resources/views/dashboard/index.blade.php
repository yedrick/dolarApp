@extends('layouts.app')
@section('title', 'Mi panel')

@section('content')
<div class="container">

    {{-- Header del panel mejorado --}}
    <div class="page-header">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div class="user-avatar" style="width: 56px; height: 56px; font-size: 1.5rem;">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div>
                <p style="font-size: 0.9rem; color: var(--color-text-muted); margin-bottom: 0.25rem;">Bienvenido de vuelta</p>
                <h1 class="page-title" style="font-size: 1.75rem;">{{ auth()->user()->name }}</h1>
            </div>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('chat.index') }}" class="btn btn--ghost">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;">
                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                </svg>
                Mensajes
            </a>
            <a href="{{ route('offers.create') }}" class="btn btn--primary btn--lg">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Nueva oferta
            </a>
        </div>
    </div>

    {{-- Stats rápidas mejoradas --}}
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 2.5rem;">
        <div style="background: var(--color-surface); border: 1px solid var(--color-border-soft); border-radius: var(--radius-xl); padding: 1.5rem; position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; right: 0; width: 80px; height: 80px; background: linear-gradient(135deg, var(--color-primary-l) 0%, transparent 100%); border-radius: 0 0 0 80px; opacity: 0.5;"></div>
            <span style="font-size: 0.8rem; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Ofertas activas</span>
            <span style="display: block; font-size: 2.5rem; font-weight: 700; color: var(--color-primary); margin-top: 0.5rem; font-family: var(--font-mono);">{{ $activeOffersCount }}</span>
        </div>
        <div style="background: var(--color-surface); border: 1px solid var(--color-border-soft); border-radius: var(--radius-xl); padding: 1.5rem; position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; right: 0; width: 80px; height: 80px; background: linear-gradient(135deg, var(--color-border-soft) 0%, transparent 100%); border-radius: 0 0 0 80px; opacity: 0.5;"></div>
            <span style="font-size: 0.8rem; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Total publicadas</span>
            <span style="display: block; font-size: 2.5rem; font-weight: 700; color: var(--color-text); margin-top: 0.5rem; font-family: var(--font-mono);">{{ $totalOffersCount }}</span>
        </div>
        <div style="background: var(--color-surface); border: 1px solid var(--color-border-soft); border-radius: var(--radius-xl); padding: 1.5rem; position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; right: 0; width: 80px; height: 80px; background: linear-gradient(135deg, var(--color-sell-bg) 0%, transparent 100%); border-radius: 0 0 0 80px; opacity: 0.5;"></div>
            <span style="font-size: 0.8rem; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Dólar paralelo</span>
            <span style="display: block; font-size: 2rem; font-weight: 700; color: var(--color-sell); margin-top: 0.5rem; font-family: var(--font-mono);">
                Bs. {{ number_format($paraleloRate?->sellPrice ?? 0, 2) }}
            </span>
            <small style="color: var(--color-text-hint);">Venta</small>
        </div>
        <div style="background: var(--color-surface); border: 1px solid var(--color-border-soft); border-radius: var(--radius-xl); padding: 1.5rem; position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; right: 0; width: 80px; height: 80px; background: linear-gradient(135deg, var(--color-active-bg) 0%, transparent 100%); border-radius: 0 0 0 80px; opacity: 0.5;"></div>
            <span style="font-size: 0.8rem; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Dólar oficial</span>
            <span style="display: block; font-size: 2rem; font-weight: 700; color: var(--color-active); margin-top: 0.5rem; font-family: var(--font-mono);">
                Bs. {{ number_format($oficialRate?->sellPrice ?? 0, 2) }}
            </span>
            <small style="color: var(--color-text-hint);">Venta</small>
        </div>
    </div>

    {{-- Conversor rápido mejorado --}}
    <section class="section">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--color-primary-l) 0%, var(--color-primary) 100%); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23"/>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
            </div>
            <h2 class="section-title" style="margin: 0;">Conversor rápido</h2>
        </div>
        <div style="background: var(--color-surface); border: 1px solid var(--color-border-soft); border-radius: var(--radius-xl); padding: 2rem; box-shadow: var(--shadow-sm);">
            <div class="converter-row">
                <div class="form-group" style="flex:1; min-width: 150px;">
                    <label class="form-label">Monto</label>
                    <input type="number" id="conv-amount" class="form-input form-input--lg" value="100" min="0.01" step="0.01">
                </div>
                <div class="form-group" style="flex:1; min-width: 150px;">
                    <label class="form-label">Desde</label>
                    <select id="conv-from" class="form-input form-select form-input--lg">
                        <option value="BOB">🇧🇴 Bolivianos (BOB)</option>
                        <option value="USD">🇺🇸 Dólares (USD)</option>
                    </select>
                </div>
                <div class="form-group" style="flex:1; min-width: 180px;">
                    <label class="form-label">Tipo de cambio</label>
                    <select id="conv-type" class="form-input form-select form-input--lg">
                        <option value="oficial">Oficial</option>
                        <option value="paralelo" selected>Paralelo</option>
                        <option value="librecambista">Librecambista</option>
                    </select>
                </div>
                <div class="converter-action" style="min-width: 140px;">
                    <label class="form-label" style="visibility:hidden">.</label>
                    <button id="conv-btn" class="btn btn--primary btn--lg btn--block">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;">
                            <polyline points="23 4 23 10 17 10"/>
                            <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                        </svg>
                        Convertir
                    </button>
                </div>
            </div>
            <div id="conv-result" style="display:none; margin-top: 1.5rem; padding: 1.5rem; background: linear-gradient(135deg, var(--color-primary-l) 0%, rgba(0,113,227,0.05) 100%); border-radius: var(--radius-lg); border: 1px solid var(--color-primary-l);">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <span id="conv-output" style="font-size: 2.5rem; font-weight: 700; color: var(--color-primary); font-family: var(--font-mono);"></span>
                        <span id="conv-direction" style="display: block; margin-top: 0.25rem; color: var(--color-text-muted); font-size: 0.9rem;"></span>
                    </div>
                    <button onclick="document.getElementById('conv-result').style.display='none'" class="btn btn--ghost btn--sm">
                        Ocultar
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- Mis ofertas mejoradas --}}
    <section class="section">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--color-buy-bg) 0%, var(--color-buy) 100%); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                    </svg>
                </div>
                <h2 class="section-title" style="margin: 0;">Mis ofertas</h2>
            </div>
            <a href="{{ route('offers.index') }}" class="btn btn--ghost btn--sm">
                Ver mercado completo
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-left: 0.3rem;">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </a>
        </div>

        @if($myOffers->isEmpty())
            <div class="empty-state" style="padding: 3rem 2rem;">
                <div style="width: 80px; height: 80px; background: var(--color-bg); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-hint)" stroke-width="1.5">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                    </svg>
                </div>
                <p class="empty-state__text" style="font-size: 1.1rem; margin-bottom: 0.5rem;">Aún no tienes ofertas publicadas</p>
                <p style="color: var(--color-text-muted); margin-bottom: 1.5rem;">Publica tu primera oferta y empieza a cambiar divisas</p>
                <a href="{{ route('offers.create') }}" class="btn btn--primary btn--lg">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Publicar mi primera oferta
                </a>
            </div>
        @else
            <div style="background: var(--color-surface); border: 1px solid var(--color-border-soft); border-radius: var(--radius-xl); overflow: hidden; box-shadow: var(--shadow-sm);">
                <table class="offers-table" style="border: none;">
                    <thead>
                        <tr style="background: var(--color-bg);">
                            <th style="font-weight: 600;">Tipo</th>
                            <th style="font-weight: 600;">Precio</th>
                            <th style="font-weight: 600;">Monto</th>
                            <th style="font-weight: 600;">Estado</th>
                            <th style="font-weight: 600;">Publicada</th>
                            <th style="font-weight: 600; text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($myOffers as $offer)
                        <tr>
                            <td>
                                <span class="badge badge--{{ $offer->type === 'venta' ? 'sell' : 'buy' }} badge--lg">
                                    {{ ucfirst($offer->type) }}
                                </span>
                            </td>
                            <td class="font-mono" style="font-size: 1.1rem; font-weight: 600;">Bs. {{ number_format($offer->price, 2) }}</td>
                            <td>
                                <div class="font-mono" style="font-size: 1.1rem; font-weight: 600;">$ {{ number_format($offer->amount, 2) }}</div>
                                @if($offer->reservedAmount > 0)
                                    <small style="color: var(--color-sell);">En negociación: $ {{ number_format($offer->reservedAmount, 2) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge--{{ $offer->status === 'activa' ? 'active' : 'closed' }} badge--lg">
                                    {{ ucfirst($offer->status) }}
                                </span>
                            </td>
                            <td class="text-muted text-sm">{{ \Carbon\Carbon::parse($offer->createdAt)->diffForHumans() }}</td>
                            <td style="text-align: right;">
                                @if($offer->status === 'activa')
                                    <form method="POST" action="{{ route('offers.close', $offer->id) }}"
                                          onsubmit="return confirm('¿Estás seguro de cerrar esta oferta?')"
                                          style="display: inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn--ghost btn--sm" style="color: var(--color-sell);">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.3rem;">
                                                <line x1="18" y1="6" x2="6" y2="18"/>
                                                <line x1="6" y1="6" x2="18" y2="18"/>
                                            </svg>
                                            Cerrar
                                        </button>
                                    </form>
                                @else
                                    <span style="color: var(--color-text-hint); font-size: 0.85rem;">Cerrada</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

</div>
@endsection

@push('scripts')
<script>
document.getElementById('conv-btn').addEventListener('click', async () => {
    const amount = document.getElementById('conv-amount').value;
    const from   = document.getElementById('conv-from').value;
    const type   = document.getElementById('conv-type').value;

    const btn = document.getElementById('conv-btn');
    btn.textContent = '...';
    btn.disabled = true;

    try {
        const res = await fetch('/api/v1/exchange-rates/convert', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ amount: parseFloat(amount), from, rate_type: type }),
        });

        if (!res.ok) {
            throw new Error('Error HTTP: ' + res.status);
        }

        const data = await res.json();

        if (data.success) {
            const r = data.data;
            const toLabel = from === 'BOB' ? 'USD' : 'BOB';
            document.getElementById('conv-output').textContent =
                (from === 'BOB' ? '$ ' : 'Bs. ') + parseFloat(r.output).toFixed(2);
            document.getElementById('conv-direction').textContent =
                r.direction + ' · tipo ' + type;
            document.getElementById('conv-result').style.display = 'flex';
        } else {
            alert(data.message || 'Error al convertir');
        }
    } catch (e) {
        console.error('Error:', e);
        alert('Error de conexión: ' + e.message);
    } finally {
        btn.textContent = 'Convertir';
        btn.disabled = false;
    }
});
</script>
@endpush

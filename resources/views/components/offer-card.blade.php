{{-- Componente: offer-card --}}
{{-- Uso: @include('components.offer-card', ['offer' => $offer]) --}}

<div class="offer-card">
    <div class="offer-card__header">
        <span class="badge badge--{{ $offer->type === 'venta' ? 'sell' : 'buy' }} badge--lg">
            {{ $offer->type === 'venta' ? 'VENTA' : 'COMPRA' }}
        </span>
        <span class="offer-card__time text-muted text-sm">
            {{ \Carbon\Carbon::parse($offer->createdAt)->diffForHumans() }}
        </span>
    </div>

    <div class="offer-card__price">
        <span class="price-main font-mono">Bs. {{ number_format($offer->price, 2) }}</span>
        <span class="price-label text-muted">por dólar</span>
    </div>

    <div class="offer-card__amount">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span class="text-muted text-sm">Disponible:</span>
            <span class="font-mono" style="font-size: 1.1rem; font-weight: 600;">$ {{ number_format($offer->availableAmount, 2) }}</span>
        </div>
        @if($offer->reservedAmount > 0)
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.25rem;">
                <small style="color: var(--color-text-hint);">En negociación:</small>
                <small style="color: var(--color-sell); font-weight: 500;">$ {{ number_format($offer->reservedAmount, 2) }}</small>
            </div>
        @endif
        @if($offer->availableAmount < $offer->amount)
            <div style="margin-top: 0.5rem;">
                <div style="height: 4px; background: var(--color-border-soft); border-radius: 2px; overflow: hidden;">
                    @php
                        $percentAvailable = ($offer->availableAmount / $offer->amount) * 100;
                    @endphp
                    <div style="width: {{ $percentAvailable }}%; height: 100%; background: linear-gradient(90deg, var(--color-buy) 0%, var(--color-primary) 100%); border-radius: 2px; transition: width 0.3s ease;"></div>
                </div>
                <small style="color: var(--color-text-hint); display: block; margin-top: 0.25rem; text-align: right;">
                    {{ round($percentAvailable) }}% disponible
                </small>
            </div>
        @endif
    </div>

    @if($offer->notes)
        <p class="offer-card__notes text-sm text-muted">
            "{{ Str::limit($offer->notes, 80) }}"
        </p>
    @endif

    <div class="offer-card__footer">
        @auth
            @if($offer->userId !== auth()->id())
                @if($offer->contactInfo)
                    <button type="button"
                            class="btn btn--primary btn--sm btn--block"
                            onclick="showContactModal({{ $offer->id }}, {{ $offer->userId }}, {{ $offer->id }})">
                        Contactar
                    </button>
                @else
                    <span class="text-muted text-sm">Sin contacto disponible</span>
                @endif
            @else
                <span class="text-muted text-sm">Tu oferta</span>
            @endif
        @else
            <a href="{{ route('login') }}" class="btn btn--primary btn--sm btn--block">
                Login para contactar
            </a>
        @endauth
    </div>
</div>

{{-- Modal de contacto y chat --}}
<div id="contact-modal-{{ $offer->id }}" class="modal-overlay" style="display:none" onclick="if(event.target === this) closeModal({{ $offer->id }})">
    <div class="modal">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <span class="badge badge--{{ $offer->type === 'venta' ? 'sell' : 'buy' }} badge--lg" style="margin-bottom: 0.75rem;">
                {{ $offer->type === 'venta' ? 'VENTA' : 'COMPRA' }}
            </span>
            <h3 class="modal__title" style="margin-bottom: 0.25rem;">Contactar vendedor</h3>
            <p style="color: var(--color-text-muted); font-size: 0.9rem;">
                Bs. {{ number_format($offer->price, 2) }} por dólar
            </p>
        </div>

        @if($offer->contactInfo)
            <p class="modal__body" style="margin-bottom: 1rem;">
                <span class="font-mono modal__phone">{{ $offer->contactInfo }}</span>
                <small style="color: var(--color-text-muted); display: block; margin-top: 0.25rem;">Teléfono de contacto</small>
            </p>

            <div class="modal__actions" style="flex-direction: column; gap: 0.75rem;">
                <a href="tel:{{ $offer->contactInfo }}" class="btn btn--primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.5rem;">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                    </svg>
                    Llamar
                </a>
                <a href="https://wa.me/591{{ ltrim($offer->contactInfo, '0') }}" target="_blank" class="btn btn--whatsapp">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 0.5rem;">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413"/>
                    </svg>
                    WhatsApp
                </a>

                <div style="border-top: 1px solid var(--color-border); padding-top: 1rem; margin-top: 0.5rem;">
                    <p style="font-size: 0.85rem; color: var(--color-text-muted); text-align: center; margin-bottom: 1rem;">
                        O envía un mensaje directo
                    </p>
                    @auth
                        <form action="{{ route('chat.start') }}" method="POST" style="width: 100%;">
                            @csrf
                            <input type="hidden" name="receiver_id" value="{{ $offer->userId }}">
                            <input type="hidden" name="offer_id" value="{{ $offer->id }}">

                            <div class="form-group" style="margin-bottom: 1rem;">
                                <label class="form-label" style="font-size: 0.875rem; margin-bottom: 0.5rem;">
                                    ¿Cuánto quieres cambiar? (USD)
                                </label>
                                <input type="number" name="amount" id="amount-{{ $offer->id }}"
                                       step="0.01" min="1" max="{{ $offer->availableAmount }}"
                                       placeholder="Ej: 100" required
                                       class="form-input form-input--lg">
                                <small style="color: var(--color-text-muted); font-size: 0.8rem; display: block; margin-top: 0.25rem;">
                                    Disponible: $ {{ number_format($offer->availableAmount, 2) }} de $ {{ number_format($offer->amount, 2) }}
                                </small>
                            </div>

                            <div class="form-group" style="margin-bottom: 1rem;">
                                <textarea name="message" id="message-{{ $offer->id }}" class="form-input form-textarea"
                                          rows="2" placeholder="Mensaje opcional..."></textarea>
                            </div>

                            <button type="submit" class="btn btn--primary btn--block btn--lg" style="margin-bottom: 0.5rem;">
                                Enviar mensaje
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn--primary btn--block btn--lg">
                            Inicia sesión para contactar
                        </a>
                    @endauth
                </div>
            </div>
        @else
            <div class="empty-state" style="padding: 2rem 1rem;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-hint)" stroke-width="1.5">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
                <p class="text-muted">El usuario no ha proporcionado información de contacto.</p>
            </div>
        @endif
        <div style="margin-top: 1.5rem; text-align: center;">
            <button onclick="closeModal({{ $offer->id }})" class="btn btn--ghost btn--sm">
                Cerrar
            </button>
        </div>
    </div>
</div>

<script>
function showContactModal(offerId) {
    const modal = document.getElementById('contact-modal-' + offerId);
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeModal(offerId) {
    const modal = document.getElementById('contact-modal-' + offerId);
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}
// Cerrar modal con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay').forEach(m => {
            m.style.display = 'none';
        });
        document.body.style.overflow = 'auto';
    }
});
</script>

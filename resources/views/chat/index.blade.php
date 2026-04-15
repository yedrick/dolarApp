@extends('layouts.app')
@section('title', 'Mensajes')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Mensajes</h1>
            <p class="page-subtitle">Tus conversaciones</p>
        </div>
    </div>

    @if(empty($conversations))
        <div class="empty-state">
            <p class="empty-state__text">No tienes conversaciones aún.</p>
            <a href="{{ route('offers.index') }}" class="btn btn--primary">Ver ofertas</a>
        </div>
    @else
        <div class="conversations-list">
            @foreach($conversations as $conv)
                <a href="{{ route('chat.show', $conv['id']) }}" class="conversation-item">
                    <div class="conversation-avatar">
                        {{ substr($conv['other_user']['name'], 0, 1) }}
                    </div>
                    <div class="conversation-info">
                        <div class="conversation-header">
                            <span class="conversation-name">{{ $conv['other_user']['name'] }}</span>
                            @if($conv['last_message'])
                                <span class="conversation-time">{{ \Carbon\Carbon::parse($conv['last_message_at'])->diffForHumans() }}</span>
                            @endif
                        </div>
                        <div class="conversation-offer">
                            <span class="badge badge--{{ $conv['offer']['type'] === 'venta' ? 'sell' : 'buy' }}">
                                {{ $conv['offer']['type'] }}
                            </span>
                            <span class="offer-price">Bs. {{ number_format($conv['offer']['price'], 2) }}</span>
                        </div>
                        @if($conv['last_message'])
                            <p class="conversation-preview {{ $conv['last_message']['is_mine'] ? 'text-muted' : '' }}">
                                {{ $conv['last_message']['is_mine'] ? 'Tú: ' : '' }}{{ Str::limit($conv['last_message']['content'], 50) }}
                            </p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection

<style>
.conversations-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.conversation-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background: var(--bg-card);
    border-radius: 8px;
    text-decoration: none;
    color: inherit;
    transition: background 0.2s;
}

.conversation-item:hover {
    background: var(--bg-hover);
}

.conversation-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    flex-shrink: 0;
}

.conversation-info {
    flex: 1;
    min-width: 0;
}

.conversation-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.25rem;
}

.conversation-name {
    font-weight: 600;
}

.conversation-time {
    font-size: 0.75rem;
    color: var(--text-muted);
}

.conversation-offer {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.25rem;
}

.offer-price {
    font-size: 0.875rem;
    color: var(--text-muted);
}

.conversation-preview {
    font-size: 0.875rem;
    color: var(--text-muted);
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>

@extends('layouts.app')
@section('title', 'Chat con ' . $otherUser->name)

@section('content')
<div class="container">
    <div class="chat-header">
        <a href="{{ route('chat.index') }}" class="back-link">« Volver</a>
        <div class="chat-user-info">
            <div class="chat-avatar">
                {{ substr($otherUser->name, 0, 1) }}
            </div>
            <div>
                <h2 class="chat-user-name">{{ $otherUser->name }}</h2>
                <div class="chat-offer-info">
                    <span class="badge badge--{{ $conversation->offer->type === 'venta' ? 'sell' : 'buy' }}">
                        {{ $conversation->offer->type }}
                    </span>
                    <span>Bs. {{ number_format($conversation->offer->price, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="chat-messages" id="chatMessages">
        @forelse($messages as $msg)
            <div class="message {{ $msg['sender_id'] === auth()->id() ? 'message--sent' : 'message--received' }}">
                <div class="message-content">
                    {{ $msg['content'] }}
                </div>
                <div class="message-meta">
                    <span class="message-time">{{ \Carbon\Carbon::parse($msg['created_at'])->format('H:i') }}</span>
                    @if($msg['sender_id'] === auth()->id())
                        <span class="message-status">{{ $msg['is_read'] ? 'Leído' : 'Enviado' }}</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="chat-empty">
                <p>No hay mensajes aún. ¡Inicia la conversación!</p>
            </div>
        @endforelse
    </div>

    <form method="POST" action="{{ route('chat.send', $conversation->id) }}" class="chat-form" id="chatForm">
        @csrf
        <input type="text" name="message" class="chat-input" placeholder="Escribe un mensaje..." required maxlength="1000">
        <button type="submit" class="btn btn--primary">Enviar</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('chatMessages');
    messagesContainer.scrollTop = messagesContainer.scrollHeight;

    const form = document.getElementById('chatForm');
    form.addEventListener('submit', function() {
        setTimeout(() => {
            location.reload();
        }, 100);
    });
});
</script>
@endpush

<style>
.chat-header {
    margin-bottom: 1rem;
}

.back-link {
    display: inline-block;
    margin-bottom: 1rem;
    color: var(--primary);
    text-decoration: none;
}

.back-link:hover {
    text-decoration: underline;
}

.chat-user-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--bg-card);
    border-radius: 8px;
}

.chat-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.25rem;
}

.chat-user-name {
    font-size: 1.125rem;
    margin: 0 0 0.25rem 0;
}

.chat-offer-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: var(--text-muted);
}

.chat-messages {
    height: 400px;
    overflow-y: auto;
    padding: 1rem;
    background: var(--bg-card);
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.message {
    max-width: 70%;
    display: flex;
    flex-direction: column;
}

.message--sent {
    align-self: flex-end;
}

.message--received {
    align-self: flex-start;
}

.message-content {
    padding: 0.75rem 1rem;
    border-radius: 12px;
    word-wrap: break-word;
}

.message--sent .message-content {
    background: var(--primary);
    color: white;
    border-bottom-right-radius: 4px;
}

.message--received .message-content {
    background: var(--bg-hover);
    color: var(--text);
    border-bottom-left-radius: 4px;
}

.message-meta {
    display: flex;
    gap: 0.5rem;
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-top: 0.25rem;
    padding: 0 0.25rem;
}

.message--sent .message-meta {
    justify-content: flex-end;
}

.chat-empty {
    text-align: center;
    color: var(--text-muted);
    padding: 2rem;
}

.chat-form {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

.chat-input {
    flex: 1;
    padding: 0.75rem 1rem;
    border: 1px solid var(--border);
    border-radius: 8px;
    font-size: 1rem;
}

.chat-input:focus {
    outline: none;
    border-color: var(--primary);
}
</style>

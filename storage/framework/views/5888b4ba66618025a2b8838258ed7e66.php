<?php $__env->startSection('title', 'Chat con ' . $otherUser->name); ?>

<?php $__env->startSection('content'); ?>
<div class="chat-container">
    <div class="chat-header">
        <a href="<?php echo e(route('chat.index')); ?>" class="back-link">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
            Volver
        </a>
        <div class="chat-user-info">
            <div class="chat-avatar">
                <?php echo e(strtoupper(substr($otherUser->name, 0, 1))); ?>

            </div>
            <div class="chat-user-details">
                <h2 class="chat-user-name"><?php echo e($otherUser->name); ?></h2>
                <div class="chat-offer-info">
                    <span class="badge badge--<?php echo e($conversation->offer->type === 'venta' ? 'sell' : 'buy'); ?>">
                        <?php echo e(strtoupper($conversation->offer->type)); ?>

                    </span>
                    <span class="chat-price">Bs. <?php echo e(number_format($conversation->offer->price, 2)); ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="chat-messages" id="chatMessages">
        <?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="message <?php echo e($msg['sender_id'] === auth()->id() ? 'message--sent' : 'message--received'); ?>" data-message-id="<?php echo e($msg['id']); ?>">
                <div class="message-bubble">
                    <?php if($msg['image_path']): ?>
                        <div class="message-image">
                            <img src="<?php echo e(asset('storage/' . $msg['image_path'])); ?>" alt="Imagen" onclick="window.open(this.src, '_blank')">
                        </div>
                    <?php endif; ?>
                    <?php if($msg['content']): ?>
                        <div class="message-text"><?php echo e($msg['content']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="message-meta">
                    <span class="message-time"><?php echo e(\Carbon\Carbon::parse($msg['created_at'])->format('H:i')); ?></span>
                    <?php if($msg['sender_id'] === auth()->id()): ?>
                        <span class="message-status">
                            <?php if($msg['is_read']): ?>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                Leído
                            <?php else: ?>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                Enviado
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="chat-empty">
                <div class="chat-empty-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                    </svg>
                </div>
                <p>No hay mensajes aún. ¡Inicia la conversación!</p>
            </div>
        <?php endif; ?>
    </div>

    <form method="POST" action="<?php echo e(route('chat.send', $conversation->id)); ?>" class="chat-form" id="chatForm" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="chat-input-wrapper">
            <label for="imageInput" class="chat-attach-btn" title="Adjuntar imagen">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                    <polyline points="21 15 16 10 5 21"></polyline>
                </svg>
            </label>
            <input type="file" id="imageInput" name="image" accept="image/*" style="display:none">
            <input type="text" name="message" class="chat-input" placeholder="Escribe un mensaje..." maxlength="1000">
            <button type="submit" class="chat-send-btn" title="Enviar">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                </svg>
            </button>
        </div>
        <div id="imagePreview" class="image-preview" style="display:none">
            <img src="" alt="Preview">
            <button type="button" class="image-preview-remove" onclick="removeImage()">×</button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('chatMessages');
    const chatForm = document.getElementById('chatForm');
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = imagePreview.querySelector('img');

    // Scroll al final de los mensajes
    messagesContainer.scrollTop = messagesContainer.scrollHeight;

    // Auto-refresh cada 5 segundos para simular tiempo real
    let lastMessageCount = <?php echo e(count($messages)); ?>;
    setInterval(function() {
        fetch('<?php echo e(route("chat.messages", $conversation->id)); ?>')
            .then(response => response.json())
            .then(data => {
                if (data.messages && data.messages.length !== lastMessageCount) {
                    location.reload();
                }
            })
            .catch(err => console.log('Refresh error:', err));
    }, 5000);

    // Preview de imagen seleccionada
    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'flex';
            };
            reader.readAsDataURL(file);
        }
    });

    // Enviar mensaje con animación
    chatForm.addEventListener('submit', function(e) {
        const messageInput = this.querySelector('input[name="message"]');
        const hasImage = imageInput.files.length > 0;

        if (!messageInput.value.trim() && !hasImage) {
            e.preventDefault();
            return;
        }
    });
});

function removeImage() {
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    imageInput.value = '';
    imagePreview.style.display = 'none';
}
</script>
<?php $__env->stopPush(); ?>

<style>
:root {
    --chat-bg: #f0f2f5;
    --chat-bubble-sent: #0071e3;
    --chat-bubble-received: #ffffff;
    --chat-text-sent: #ffffff;
    --chat-text-received: #1d1d1f;
}

.chat-container {
    max-width: 900px;
    margin: 0 auto;
    height: calc(100vh - 180px);
    display: flex;
    flex-direction: column;
}

.chat-header {
    background: var(--color-surface);
    border-radius: var(--radius-lg);
    padding: 1rem 1.5rem;
    margin-bottom: 1rem;
    box-shadow: var(--shadow-sm);
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    color: var(--color-primary);
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    transition: color 0.2s;
}

.back-link:hover {
    color: var(--color-primary-d);
}

.chat-user-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.chat-avatar {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-h) 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.4rem;
    box-shadow: 0 4px 12px rgba(0,113,227,0.3);
}

.chat-user-details {
    flex: 1;
}

.chat-user-name {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0 0 0.35rem 0;
    color: var(--color-text);
}

.chat-offer-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.875rem;
}

.chat-price {
    color: var(--color-text-muted);
    font-weight: 500;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 1.5rem;
    background: var(--chat-bg);
    border-radius: var(--radius-lg);
    display: flex;
    flex-direction: column;
    gap: 1rem;
    box-shadow: inset 0 2px 8px rgba(0,0,0,0.04);
}

.message {
    max-width: 75%;
    display: flex;
    flex-direction: column;
    animation: messageIn 0.3s ease;
}

@keyframes messageIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.message--sent {
    align-self: flex-end;
    align-items: flex-end;
}

.message--received {
    align-self: flex-start;
    align-items: flex-start;
}

.message-bubble {
    padding: 0.875rem 1.25rem;
    border-radius: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    max-width: 100%;
}

.message-image {
    margin-bottom: 0.5rem;
}

.message-image img {
    max-width: 280px;
    max-height: 200px;
    border-radius: 12px;
    cursor: pointer;
    transition: transform 0.2s;
}

.message-image img:hover {
    transform: scale(1.02);
}

.message-text {
    word-wrap: break-word;
    line-height: 1.5;
}

.message--sent .message-bubble {
    background: var(--chat-bubble-sent);
    color: var(--chat-text-sent);
    border-bottom-right-radius: 4px;
}

.message--received .message-bubble {
    background: var(--chat-bubble-received);
    color: var(--chat-text-received);
    border-bottom-left-radius: 4px;
}

.message-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    color: var(--color-text-hint);
    margin-top: 0.35rem;
    padding: 0 0.5rem;
}

.message-status {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.message-status svg {
    width: 12px;
    height: 12px;
}

.chat-empty {
    text-align: center;
    color: var(--color-text-muted);
    padding: 3rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.chat-empty-icon {
    color: var(--color-text-hint);
    opacity: 0.5;
}

.chat-form {
    margin-top: 1rem;
    background: var(--color-surface);
    border-radius: var(--radius-lg);
    padding: 1rem;
    box-shadow: var(--shadow-sm);
}

.chat-input-wrapper {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.chat-attach-btn {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--color-text-muted);
    transition: all 0.2s;
    flex-shrink: 0;
}

.chat-attach-btn:hover {
    background: var(--color-bg);
    color: var(--color-primary);
}

.chat-input {
    flex: 1;
    padding: 0.875rem 1.25rem;
    border: 1.5px solid var(--color-border);
    border-radius: 28px;
    font-size: 1rem;
    background: var(--color-bg);
    transition: all 0.2s;
}

.chat-input:focus {
    outline: none;
    border-color: var(--color-primary);
    background: var(--color-surface);
}

.chat-send-btn {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: var(--color-primary);
    color: white;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(0,113,227,0.3);
}

.chat-send-btn:hover {
    background: var(--color-primary-h);
    transform: scale(1.05);
}

.image-preview {
    margin-top: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem;
    background: var(--color-bg);
    border-radius: var(--radius-md);
}

.image-preview img {
    max-height: 80px;
    max-width: 120px;
    border-radius: var(--radius-sm);
}

.image-preview-remove {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: none;
    background: var(--color-sell);
    color: white;
    font-size: 1.2rem;
    line-height: 1;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s;
}

.image-preview-remove:hover {
    transform: scale(1.1);
}

@media (max-width: 640px) {
    .chat-container {
        height: calc(100vh - 140px);
    }

    .message {
        max-width: 85%;
    }

    .message-image img {
        max-width: 200px;
    }

    .chat-header {
        padding: 0.75rem 1rem;
    }

    .chat-avatar {
        width: 44px;
        height: 44px;
        font-size: 1.1rem;
    }
}
</style>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\collatech\dolarapp\resources\views/chat/show.blade.php ENDPATH**/ ?>
<?php $__env->startSection('title', 'Mensajes'); ?>

<?php $__env->startSection('content'); ?>
<div class="chat-list-container">
    <div class="chat-list-header">
        <div class="chat-list-title-wrapper">
            <div class="chat-list-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                </svg>
            </div>
            <div>
                <h1 class="chat-list-title">Mensajes</h1>
                <p class="chat-list-subtitle"><?php echo e(count($conversations)); ?> conversaciones activas</p>
            </div>
        </div>
        <a href="<?php echo e(route('offers.index')); ?>" class="btn btn--primary btn--sm">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Nueva conversación
        </a>
    </div>

    <?php if(empty($conversations)): ?>
        <div class="chat-empty-state">
            <div class="chat-empty-icon">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                </svg>
            </div>
            <h3 class="chat-empty-title">No tienes conversaciones aún</h3>
            <p class="chat-empty-text">Encuentra ofertas en el mercado y contacta con vendedores o compradores</p>
            <a href="<?php echo e(route('offers.index')); ?>" class="btn btn--primary btn--lg">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Ver ofertas disponibles
            </a>
        </div>
    <?php else: ?>
        <div class="conversations-wrapper">
            <?php $__currentLoopData = $conversations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('chat.show', $conv['id'])); ?>" class="conversation-card <?php echo e(!$conv['last_message']['is_read'] && !$conv['last_message']['is_mine'] ? 'conversation-card--unread' : ''); ?>">
                    <div class="conversation-avatar-wrapper">
                        <div class="conversation-avatar">
                            <?php echo e(strtoupper(substr($conv['other_user']['name'], 0, 1))); ?>

                        </div>
                        <div class="conversation-status online"></div>
                    </div>
                    <div class="conversation-content">
                        <div class="conversation-main">
                            <div class="conversation-top">
                                <h3 class="conversation-name"><?php echo e($conv['other_user']['name']); ?></h3>
                                <?php if($conv['last_message']): ?>
                                    <span class="conversation-time <?php echo e(!$conv['last_message']['is_read'] && !$conv['last_message']['is_mine'] ? 'conversation-time--unread' : ''); ?>">
                                        <?php echo e(\Carbon\Carbon::parse($conv['last_message_at'])->diffForHumans(short: true)); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="conversation-offer-info">
                                <span class="offer-badge badge--<?php echo e($conv['offer']['type'] === 'venta' ? 'sell' : 'buy'); ?>">
                                    <?php echo e(strtoupper($conv['offer']['type'])); ?>

                                </span>
                                <span class="offer-price">Bs. <?php echo e(number_format($conv['offer']['price'], 2)); ?></span>
                            </div>
                        </div>
                        <div class="conversation-bottom">
                            <?php if($conv['last_message']): ?>
                                <p class="conversation-message <?php echo e(!$conv['last_message']['is_read'] && !$conv['last_message']['is_mine'] ? 'conversation-message--unread' : ''); ?>">
                                    <?php if($conv['last_message']['is_mine']): ?>
                                        <span class="message-prefix">Tú:</span>
                                    <?php endif; ?>
                                    <?php if($conv['last_message']['image_path']): ?>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle; margin-right: 4px;">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                            <polyline points="21 15 16 10 5 21"></polyline>
                                        </svg>
                                        Foto
                                    <?php else: ?>
                                        <?php echo e(Str::limit($conv['last_message']['content'], 55)); ?>

                                    <?php endif; ?>
                                </p>
                                <?php if(!$conv['last_message']['is_read'] && !$conv['last_message']['is_mine']): ?>
                                    <span class="unread-badge">Nuevo</span>
                                <?php elseif(!$conv['last_message']['is_read'] && $conv['last_message']['is_mine']): ?>
                                    <span class="unread-badge unread-badge--sent">No leído</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="conversation-message conversation-message--empty">Sin mensajes aún</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="conversation-arrow">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<style>
.chat-list-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 1rem;
}

.chat-list-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    padding: 1rem 0;
    border-bottom: 1px solid var(--color-border);
}

.chat-list-title-wrapper {
    display: flex;
    align-items: center;
    gap: 0.875rem;
}

.chat-list-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-h) 100%);
    color: white;
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0,113,227,0.3);
}

.chat-list-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    color: var(--color-text);
}

.chat-list-subtitle {
    font-size: 0.875rem;
    color: var(--color-text-muted);
    margin: 0.25rem 0 0 0;
}

.conversations-wrapper {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.conversation-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    background: var(--color-surface);
    border-radius: var(--radius-xl);
    text-decoration: none;
    color: inherit;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid var(--color-border);
    box-shadow: var(--shadow-sm);
}

.conversation-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    border-color: var(--color-primary-l);
}

.conversation-card--unread {
    background: linear-gradient(135deg, var(--color-surface) 0%, var(--color-primary-l) 100%);
    border-color: var(--color-primary);
}

.conversation-avatar-wrapper {
    position: relative;
    flex-shrink: 0;
}

.conversation-avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-h) 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.25rem;
    box-shadow: 0 4px 12px rgba(0,113,227,0.25);
}

.conversation-status {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 2px solid var(--color-surface);
}

.conversation-status.online {
    background: var(--color-success);
}

.conversation-content {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

.conversation-main {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.conversation-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.conversation-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--color-text);
    margin: 0;
}

.conversation-time {
    font-size: 0.8rem;
    color: var(--color-text-hint);
    font-weight: 500;
}

.conversation-time--unread {
    color: var(--color-primary);
    font-weight: 600;
}

.conversation-offer-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.offer-badge {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 0.2rem 0.6rem;
    border-radius: 999px;
}

.offer-badge.badge--sell {
    background: var(--color-sell-bg);
    color: var(--color-sell);
}

.offer-badge.badge--buy {
    background: var(--color-buy-bg);
    color: var(--color-buy);
}

.offer-price {
    font-size: 0.875rem;
    color: var(--color-text-muted);
    font-weight: 500;
}

.conversation-bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
}

.conversation-message {
    font-size: 0.9rem;
    color: var(--color-text-muted);
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    flex: 1;
}

.conversation-message--unread {
    color: var(--color-text);
    font-weight: 500;
}

.conversation-message--empty {
    color: var(--color-text-hint);
    font-style: italic;
}

.message-prefix {
    color: var(--color-text-hint);
}

.unread-badge {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.25rem 0.6rem;
    background: var(--color-primary);
    color: white;
    border-radius: 999px;
    flex-shrink: 0;
    animation: pulse 2s infinite;
}

.unread-badge--sent {
    background: var(--color-text-hint);
    animation: none;
    font-weight: 500;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.conversation-arrow {
    color: var(--color-text-hint);
    transition: all 0.2s;
}

.conversation-card:hover .conversation-arrow {
    color: var(--color-primary);
    transform: translateX(4px);
}

/* Empty State */
.chat-empty-state {
    text-align: center;
    padding: 4rem 2rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.25rem;
}

.chat-empty-icon {
    color: var(--color-text-hint);
    opacity: 0.4;
}

.chat-empty-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--color-text);
    margin: 0;
}

.chat-empty-text {
    font-size: 0.95rem;
    color: var(--color-text-muted);
    max-width: 300px;
    line-height: 1.5;
    margin: 0;
}

/* Responsive */
@media (max-width: 640px) {
    .chat-list-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }

    .conversation-card {
        padding: 1rem;
    }

    .conversation-avatar {
        width: 48px;
        height: 48px;
        font-size: 1.1rem;
    }

    .conversation-name {
        font-size: 1rem;
    }
}
</style>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\collatech\dolarapp\resources\views/chat/index.blade.php ENDPATH**/ ?>
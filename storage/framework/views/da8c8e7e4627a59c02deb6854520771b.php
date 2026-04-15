


<div class="offer-card">
    <div class="offer-card__header">
        <span class="badge badge--<?php echo e($offer->type === 'venta' ? 'sell' : 'buy'); ?> badge--lg">
            <?php echo e($offer->type === 'venta' ? 'VENTA' : 'COMPRA'); ?>

        </span>
        <span class="offer-card__time text-muted text-sm">
            <?php echo e(\Carbon\Carbon::parse($offer->createdAt)->diffForHumans()); ?>

        </span>
    </div>

    <div class="offer-card__price">
        <span class="price-main font-mono">Bs. <?php echo e(number_format($offer->price, 2)); ?></span>
        <span class="price-label text-muted">por dólar</span>
    </div>

    <div class="offer-card__amount">
        <span class="text-muted text-sm">Monto disponible:</span>
        <span class="font-mono">$ <?php echo e(number_format($offer->amount, 2)); ?></span>
    </div>

    <?php if($offer->notes): ?>
        <p class="offer-card__notes text-sm text-muted">
            "<?php echo e(Str::limit($offer->notes, 80)); ?>"
        </p>
    <?php endif; ?>

    <div class="offer-card__footer">
        <?php if(auth()->guard()->check()): ?>
            <?php if($offer->userId !== auth()->id()): ?>
                <?php if($offer->contactInfo): ?>
                    <button type="button"
                            class="btn btn--primary btn--sm btn--block"
                            onclick="showContactModal(<?php echo e($offer->id); ?>, <?php echo e($offer->userId); ?>, <?php echo e($offer->id); ?>)">
                        Contactar
                    </button>
                <?php else: ?>
                    <span class="text-muted text-sm">Sin contacto disponible</span>
                <?php endif; ?>
            <?php else: ?>
                <span class="text-muted text-sm">Tu oferta</span>
            <?php endif; ?>
        <?php else: ?>
            <a href="<?php echo e(route('login')); ?>" class="btn btn--primary btn--sm btn--block">
                Login para contactar
            </a>
        <?php endif; ?>
    </div>
</div>


<div id="contact-modal-<?php echo e($offer->id); ?>" class="modal-overlay" style="display:none">
    <div class="modal">
        <h3 class="modal__title">Contacto</h3>
        <?php if($offer->contactInfo): ?>
            <p class="modal__body">
                <span class="font-mono modal__phone"><?php echo e($offer->contactInfo); ?></span>
            </p>
            <div class="modal__actions" style="flex-direction: column; gap: 0.5rem;">
                <a href="tel:<?php echo e($offer->contactInfo); ?>" class="btn btn--primary">Llamar</a>
                <a href="https://wa.me/591<?php echo e(ltrim($offer->contactInfo, '0')); ?>" target="_blank" class="btn btn--whatsapp">WhatsApp</a>
                <?php if(auth()->guard()->check()): ?>
                    <form action="<?php echo e(route('chat.start')); ?>" method="POST" style="width: 100%;">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="receiver_id" value="<?php echo e($offer->userId); ?>">
                        <input type="hidden" name="offer_id" value="<?php echo e($offer->id); ?>">

                        <div style="margin-bottom: 0.75rem;">
                            <label style="display: block; font-size: 0.875rem; margin-bottom: 0.25rem; color: var(--text-muted);">
                                ¿Cuánto quieres cambiar? (USD)
                            </label>
                            <input type="number" name="amount" id="amount-<?php echo e($offer->id); ?>" step="0.01" min="1" max="<?php echo e($offer->amount); ?>"
                                   placeholder="Ej: 100" required
                                   style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 6px;">
                            <small style="color: var(--text-muted); font-size: 0.75rem;">
                                Disponible: $ <?php echo e(number_format($offer->amount, 2)); ?>

                            </small>
                        </div>

                        <textarea name="message" id="message-<?php echo e($offer->id); ?>" class="form-input" rows="2" placeholder="Mensaje opcional..." style="width: 100%; margin-bottom: 0.5rem;"></textarea>

                        <button type="submit" class="btn btn--secondary btn--block">Contactar</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">El usuario no ha proporcionado información de contacto.</p>
        <?php endif; ?>
        <div style="margin-top: 1rem; text-align: center;">
            <button onclick="closeModal(<?php echo e($offer->id); ?>)" class="btn-link">Cerrar</button>
        </div>
    </div>
</div>

<script>
function showContactModal(offerId, userId, offerId2) {
    document.getElementById('contact-modal-' + offerId).style.display = 'flex';
}
function closeModal(offerId) {
    document.getElementById('contact-modal-' + offerId).style.display = 'none';
}
</script>
<?php /**PATH C:\laragon\collatech\dolarapp\resources\views/components/offer-card.blade.php ENDPATH**/ ?>
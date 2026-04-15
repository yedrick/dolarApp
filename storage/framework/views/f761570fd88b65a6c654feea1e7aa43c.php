<?php $__env->startSection('title', 'Mercado de ofertas'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">

    
    <div class="page-header">
        <div>
            <h1 class="page-title">Mercado de ofertas</h1>
            <p class="page-subtitle">
                <span style="font-weight: 600; color: var(--color-primary);"><?php echo e($totalCount); ?></span> ofertas activas ahora mismo
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('dashboard')); ?>" class="btn btn--ghost">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    Mi Dashboard
                </a>
                <a href="<?php echo e(route('offers.create')); ?>" class="btn btn--primary btn--lg">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Publicar oferta
                </a>
            <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="btn btn--ghost">Ingresar</a>
                <a href="<?php echo e(route('register')); ?>" class="btn btn--primary btn--lg">
                    Registrarse para publicar
                </a>
            <?php endif; ?>
        </div>
    </div>

    
    <div style="background: var(--color-surface); border-radius: var(--radius-xl); padding: 1.25rem 1.5rem; margin-bottom: 2rem; border: 1px solid var(--color-border-soft);">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <span style="font-size: 0.9rem; font-weight: 600; color: var(--color-text-muted);">
                Tipos de cambio de referencia
            </span>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <?php $__currentLoopData = $rates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 1rem; background: var(--color-bg); border-radius: var(--radius-md);">
                        <span style="font-size: 0.8rem; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase;">
                            <?php echo e(ucfirst($rate->type)); ?>

                        </span>
                        <div style="display: flex; gap: 0.5rem; font-family: var(--font-mono); font-size: 0.9rem;">
                            <span style="color: var(--color-buy); font-weight: 500;">C: Bs.<?php echo e(number_format($rate->buyPrice, 2)); ?></span>
                            <span style="color: var(--color-border);">|</span>
                            <span style="color: var(--color-sell); font-weight: 500;">V: Bs.<?php echo e(number_format($rate->sellPrice, 2)); ?></span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <form method="GET" action="<?php echo e(route('offers.index')); ?>" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
            <div class="filter-group">
                <label class="filter-label">Tipo de oferta</label>
                <select name="type" class="form-input form-select" onchange="this.form.submit()" style="min-width: 140px;">
                    <option value="">Todas</option>
                    <option value="compra"  <?php echo e(request('type') === 'compra'  ? 'selected' : ''); ?>>Compra 💚</option>
                    <option value="venta"   <?php echo e(request('type') === 'venta'   ? 'selected' : ''); ?>>Venta ❤️</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Ordenar por</label>
                <select name="sort" class="form-input form-select" onchange="this.form.submit()" style="min-width: 160px;">
                    <option value="recent" <?php echo e(request('sort', 'recent') === 'recent' ? 'selected' : ''); ?>>Más recientes</option>
                    <option value="price_asc"  <?php echo e(request('sort') === 'price_asc'  ? 'selected' : ''); ?>>Precio: menor a mayor</option>
                    <option value="price_desc" <?php echo e(request('sort') === 'price_desc' ? 'selected' : ''); ?>>Precio: mayor a menor</option>
                </select>
            </div>
        </form>

        <?php if(request('type') || request('sort')): ?>
            <a href="<?php echo e(route('offers.index')); ?>" class="btn btn--ghost btn--sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
                Limpiar filtros
            </a>
        <?php endif; ?>
    </div>

    
    <?php if($offers->isEmpty()): ?>
        <div class="empty-state" style="padding: 4rem 2rem;">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-hint)" stroke-width="1.5" style="margin-bottom: 1rem;">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <p class="empty-state__text" style="font-size: 1.1rem; margin-bottom: 0.5rem;">No hay ofertas activas</p>
            <p style="color: var(--color-text-muted);">Intenta ajustar los filtros o vuelve más tarde</p>
        </div>
    <?php else: ?>
        <div class="offers-grid">
            <?php $__currentLoopData = $offers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('components.offer-card', ['offer' => $offer], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\collatech\dolarapp\resources\views/offers/index.blade.php ENDPATH**/ ?>
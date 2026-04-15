<?php $__env->startSection('title', 'Mercado de ofertas'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">

    <div class="page-header">
        <div>
            <h1 class="page-title">Mercado de ofertas</h1>
            <p class="page-subtitle"><?php echo e($totalCount); ?> ofertas activas ahora mismo.</p>
        </div>
        <?php if(auth()->guard()->check()): ?>
            <a href="<?php echo e(route('offers.create')); ?>" class="btn btn--primary">+ Publicar oferta</a>
        <?php else: ?>
            <a href="<?php echo e(route('register')); ?>" class="btn btn--primary">Registrarse para publicar</a>
        <?php endif; ?>
    </div>

    
    <form method="GET" action="<?php echo e(route('offers.index')); ?>" class="filters-bar">
        <div class="filter-group">
            <label class="filter-label">Tipo</label>
            <select name="type" class="form-input form-select form-select--sm" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="compra"  <?php echo e(request('type') === 'compra'  ? 'selected' : ''); ?>>Compra</option>
                <option value="venta"   <?php echo e(request('type') === 'venta'   ? 'selected' : ''); ?>>Venta</option>
            </select>
        </div>
        <div class="filter-group">
            <label class="filter-label">Ordenar por</label>
            <select name="sort" class="form-input form-select form-select--sm" onchange="this.form.submit()">
                <option value="recent" <?php echo e(request('sort', 'recent') === 'recent' ? 'selected' : ''); ?>>Más recientes</option>
                <option value="price_asc"  <?php echo e(request('sort') === 'price_asc'  ? 'selected' : ''); ?>>Precio ↑</option>
                <option value="price_desc" <?php echo e(request('sort') === 'price_desc' ? 'selected' : ''); ?>>Precio ↓</option>
            </select>
        </div>
        <?php if(request('type') || request('sort')): ?>
            <a href="<?php echo e(route('offers.index')); ?>" class="btn-link text-sm">Limpiar filtros</a>
        <?php endif; ?>
    </form>

    
    <div class="rates-bar">
        <?php $__currentLoopData = $rates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rate-pill">
                <span class="rate-pill__label"><?php echo e(ucfirst($rate->type)); ?></span>
                <span class="rate-pill__buy">C: Bs.<?php echo e(number_format($rate->buyPrice, 2)); ?></span>
                <span class="rate-pill__sell">V: Bs.<?php echo e(number_format($rate->sellPrice, 2)); ?></span>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <?php if($offers->isEmpty()): ?>
        <div class="empty-state">
            <p class="empty-state__text">No hay ofertas activas con los filtros seleccionados.</p>
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
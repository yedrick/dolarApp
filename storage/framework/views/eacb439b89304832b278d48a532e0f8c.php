<?php $__env->startSection('title', 'Página no encontrada'); ?>

<?php $__env->startSection('content'); ?>
<div class="container" style="text-align:center;padding:6rem 1rem">
    <span class="font-mono" style="font-size:5rem;font-weight:500;color:var(--color-border);display:block;margin-bottom:1rem">404</span>
    <h1 class="page-title" style="margin-bottom:0.5rem">Página no encontrada</h1>
    <p style="color:var(--color-text-muted);margin-bottom:2rem">
        La oferta o página que buscas no existe o fue eliminada.
    </p>
    <a href="<?php echo e(route('home')); ?>" class="btn btn--primary">← Volver al inicio</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\collatech\dolarapp\resources\views/errors/404.blade.php ENDPATH**/ ?>
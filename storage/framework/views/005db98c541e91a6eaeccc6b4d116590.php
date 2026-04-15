<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title'); ?> — DólarApp</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
</head>
<body class="auth-body">

<div class="auth-wrapper">
    
    <a href="<?php echo e(route('offers.index')); ?>" class="auth-logo">
        <span class="brand-icon brand-icon--lg">$</span>
        <span class="brand-name brand-name--lg">DólarApp</span>
    </a>

    
    <div class="rate-ticker">
        <span class="ticker-item">
            <span class="ticker-label">Oficial</span>
            <span class="ticker-value">Bs. 6.86</span>
        </span>
        <span class="ticker-sep">·</span>
        <span class="ticker-item">
            <span class="ticker-label">Paralelo</span>
            <span class="ticker-value">Bs. 6.97</span>
        </span>
        <span class="ticker-sep">·</span>
        <span class="ticker-item">
            <span class="ticker-label">Librecambista</span>
            <span class="ticker-value">Bs. 7.00</span>
        </span>
    </div>

    
    <div class="auth-card">
        <?php if(session('error')): ?>
            <div class="flash flash--error" style="margin-bottom:1rem"><?php echo e(session('error')); ?></div>
        <?php endif; ?>
        <?php echo $__env->yieldContent('content'); ?>
    </div>
</div>

<script src="<?php echo e(asset('js/app.js')); ?>"></script>
</body>
</html>
<?php /**PATH C:\laragon\collatech\dolarapp\resources\views/layouts/auth.blade.php ENDPATH**/ ?>
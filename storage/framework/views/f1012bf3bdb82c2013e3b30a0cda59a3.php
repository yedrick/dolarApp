<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'DólarApp'); ?> — Mercado de Cambio Bolivia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>


<nav class="navbar" id="navbar">
    <div class="container navbar__inner">
        <a href="<?php echo e(route('dashboard')); ?>" class="navbar__brand">
            <span class="brand-icon">$</span>
            <span class="brand-name">DólarApp</span>
        </a>

        <div class="navbar__links">
            <a href="<?php echo e(route('offers.index')); ?>" class="nav-link <?php echo e(request()->routeIs('offers.*') ? 'nav-link--active' : ''); ?>">
                Mercado
            </a>
            <a href="<?php echo e(route('exchange-rates.index')); ?>" class="nav-link <?php echo e(request()->routeIs('exchange-rates.*') ? 'nav-link--active' : ''); ?>">
                Tipos de cambio
            </a>

            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('chat.index')); ?>" class="nav-link <?php echo e(request()->routeIs('chat.*') ? 'nav-link--active' : ''); ?>">
                    Mensajes
                </a>
                <a href="<?php echo e(route('offers.create')); ?>" class="btn btn--primary btn--sm">
                    + Publicar oferta
                </a>
                <div class="user-menu">
                    <span class="user-avatar"><?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?></span>
                    <a href="<?php echo e(route('profile.index')); ?>" class="user-name nav-link"><?php echo e(auth()->user()->name); ?></a>
                    <form method="POST" action="<?php echo e(route('logout')); ?>" style="display:inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn-link">Salir</button>
                    </form>
                </div>
            <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="btn btn--ghost btn--sm">Ingresar</a>
                <a href="<?php echo e(route('register')); ?>" class="btn btn--primary btn--sm">Registrarse</a>
            <?php endif; ?>
        </div>
    </div>
</nav>


<?php if(session('success')): ?>
    <div class="flash flash--success"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="flash flash--error"><?php echo e(session('error')); ?></div>
<?php endif; ?>


<main class="main">
    <?php echo $__env->yieldContent('content'); ?>
</main>


<footer class="footer">
    <div class="container">
        <span>DólarApp &copy; <?php echo e(date('Y')); ?> — Mercado de divisas Bolivia</span>
        <span class="footer__types">
            Oficial · Paralelo · Librecambista
        </span>
    </div>
</footer>

<script>
    // Efecto scroll en navbar
    window.addEventListener('scroll', () => {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 10) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Auto-hide flash messages
    setTimeout(() => {
        document.querySelectorAll('.flash').forEach(f => {
            f.style.opacity = '0';
            f.style.transition = 'opacity 0.5s ease';
            setTimeout(() => f.remove(), 500);
        });
    }, 5000);
</script>
<script src="<?php echo e(asset('js/app.js')); ?>"></script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\collatech\dolarapp\resources\views/layouts/app.blade.php ENDPATH**/ ?>
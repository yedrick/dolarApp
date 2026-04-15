<?php $__env->startSection('title', 'Ingresar'); ?>

<?php $__env->startSection('content'); ?>
<h1 class="auth-title">Bienvenido de vuelta</h1>
<p class="auth-subtitle">Ingresa para publicar y gestionar tus ofertas.</p>

<form method="POST" action="<?php echo e(route('login')); ?>" class="form" novalidate>
    <?php echo csrf_field(); ?>

    <div class="form-group">
        <label class="form-label" for="email">Correo electrónico</label>
        <input
            type="email"
            id="email"
            name="email"
            class="form-input <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> form-input--error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e(old('email')); ?>"
            placeholder="tu@correo.com"
            autocomplete="email"
            autofocus
        >
        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span class="form-error"><?php echo e($message); ?></span>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
        <label class="form-label" for="password">
            Contraseña
            <a href="#" class="form-label__link">¿Olvidaste tu contraseña?</a>
        </label>
        <input
            type="password"
            id="password"
            name="password"
            class="form-input <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> form-input--error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            placeholder="••••••••"
            autocomplete="current-password"
        >
        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span class="form-error"><?php echo e($message); ?></span>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group form-group--inline">
        <label class="checkbox-label">
            <input type="checkbox" name="remember" class="checkbox-input">
            <span>Recordarme</span>
        </label>
    </div>

    <button type="submit" class="btn btn--primary btn--block">
        Ingresar
    </button>
</form>

<p class="auth-footer-text">
    ¿No tienes cuenta?
    <a href="<?php echo e(route('register')); ?>" class="link">Regístrate gratis</a>
</p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\collatech\dolarapp\resources\views/auth/login.blade.php ENDPATH**/ ?>
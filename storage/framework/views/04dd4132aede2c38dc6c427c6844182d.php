<?php $__env->startSection('title', 'Mi perfil'); ?>

<?php $__env->startSection('content'); ?>
<div class="container container--narrow">

    
    <div class="profile-header">
        <div class="profile-avatar">
            <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

        </div>
        <div>
            <p class="profile-name"><?php echo e(auth()->user()->name); ?></p>
            <p class="profile-email"><?php echo e(auth()->user()->email); ?></p>
            <div class="profile-meta">
                <div class="profile-stat">
                    <span class="profile-stat__val"><?php echo e($activeCount); ?></span>
                    <span class="profile-stat__label">Ofertas activas</span>
                </div>
                <div class="profile-stat">
                    <span class="profile-stat__val"><?php echo e($totalCount); ?></span>
                    <span class="profile-stat__label">Total publicadas</span>
                </div>
                <div class="profile-stat">
                    <span class="profile-stat__val"><?php echo e(\Carbon\Carbon::parse(auth()->user()->created_at)->format('M Y')); ?></span>
                    <span class="profile-stat__label">Miembro desde</span>
                </div>
            </div>
        </div>
    </div>

    
    <section class="section">
        <h2 class="section-title">Cambiar contraseña</h2>
        <div class="form-card">
            <form method="POST" action="<?php echo e(route('profile.password')); ?>" class="form" novalidate>
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="form-group">
                    <label class="form-label" for="current_password">Contraseña actual</label>
                    <input type="password" id="current_password" name="current_password"
                           class="form-input <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> form-input--error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           placeholder="••••••••">
                    <?php $__errorArgs = ['current_password'];
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
                    <label class="form-label" for="new_password">Nueva contraseña</label>
                    <input type="password" id="new_password" name="new_password"
                           class="form-input <?php $__errorArgs = ['new_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> form-input--error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           placeholder="Mínimo 8 caracteres">
                    <?php $__errorArgs = ['new_password'];
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
                    <label class="form-label" for="new_password_confirmation">Confirmar nueva contraseña</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                           class="form-input" placeholder="Repite la nueva contraseña">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn--primary">Actualizar contraseña</button>
                </div>
            </form>
        </div>
    </section>

    
    <section class="section">
        <h2 class="section-title" style="color:var(--color-sell)">Zona de peligro</h2>
        <div class="form-card" style="border-color:var(--color-sell)">
            <p class="text-sm" style="color:var(--color-text-muted);margin-bottom:1rem">
                Una vez que elimines tu cuenta, se borrarán todos tus datos permanentemente.
                Esta acción no se puede deshacer.
            </p>
            <form method="POST" action="<?php echo e(route('profile.destroy')); ?>"
                  onsubmit="return confirm('¿Seguro que deseas eliminar tu cuenta? Esta acción no se puede deshacer.')">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn btn--ghost" style="border-color:var(--color-sell);color:var(--color-sell)">
                    Eliminar mi cuenta
                </button>
            </form>
        </div>
    </section>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\collatech\dolarapp\resources\views/profile/index.blade.php ENDPATH**/ ?>
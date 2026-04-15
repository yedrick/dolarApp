<?php $__env->startSection('title', 'Mi panel'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">

    
    <div class="page-header">
        <div>
            <h1 class="page-title">Hola, <?php echo e(auth()->user()->name); ?></h1>
            <p class="page-subtitle">Gestiona tus ofertas y consulta el mercado.</p>
        </div>
        <a href="<?php echo e(route('offers.create')); ?>" class="btn btn--primary">
            + Nueva oferta
        </a>
    </div>

    
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-card__label">Ofertas activas</span>
            <span class="stat-card__value"><?php echo e($activeOffersCount); ?></span>
        </div>
        <div class="stat-card">
            <span class="stat-card__label">Total publicadas</span>
            <span class="stat-card__value"><?php echo e($totalOffersCount); ?></span>
        </div>
        <div class="stat-card">
            <span class="stat-card__label">Dólar paralelo hoy</span>
            <span class="stat-card__value stat-card__value--highlight">
                Bs. <?php echo e(number_format($paraleloRate?->sellPrice ?? 0, 2)); ?>

            </span>
        </div>
        <div class="stat-card">
            <span class="stat-card__label">Dólar oficial hoy</span>
            <span class="stat-card__value">
                Bs. <?php echo e(number_format($oficialRate?->sellPrice ?? 0, 2)); ?>

            </span>
        </div>
    </div>

    
    <section class="section">
        <h2 class="section-title">Conversor rápido</h2>
        <div class="converter-card">
            <div class="converter-row">
                <div class="form-group" style="flex:1">
                    <label class="form-label">Monto</label>
                    <input type="number" id="conv-amount" class="form-input" value="100" min="0.01" step="0.01">
                </div>
                <div class="form-group" style="flex:1">
                    <label class="form-label">Desde</label>
                    <select id="conv-from" class="form-input form-select">
                        <option value="BOB">Bolivianos (BOB)</option>
                        <option value="USD">Dólares (USD)</option>
                    </select>
                </div>
                <div class="form-group" style="flex:1">
                    <label class="form-label">Tipo de cambio</label>
                    <select id="conv-type" class="form-input form-select">
                        <option value="oficial">Oficial</option>
                        <option value="paralelo" selected>Paralelo</option>
                        <option value="librecambista">Librecambista</option>
                    </select>
                </div>
                <div class="converter-action">
                    <label class="form-label" style="visibility:hidden">.</label>
                    <button id="conv-btn" class="btn btn--primary">Convertir</button>
                </div>
            </div>
            <div id="conv-result" class="converter-result" style="display:none">
                <span id="conv-output" class="converter-result__value"></span>
                <span id="conv-direction" class="converter-result__label"></span>
            </div>
        </div>
    </section>

    
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">Mis ofertas</h2>
            <a href="<?php echo e(route('offers.index')); ?>" class="link">Ver mercado completo →</a>
        </div>

        <?php if($myOffers->isEmpty()): ?>
            <div class="empty-state">
                <p class="empty-state__text">Aún no tienes ofertas publicadas.</p>
                <a href="<?php echo e(route('offers.create')); ?>" class="btn btn--primary btn--sm">Publicar mi primera oferta</a>
            </div>
        <?php else: ?>
            <div class="offers-table-wrapper">
                <table class="offers-table">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Precio</th>
                            <th>Monto USD</th>
                            <th>Estado</th>
                            <th>Publicada</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $myOffers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <span class="badge badge--<?php echo e($offer->type === 'venta' ? 'sell' : 'buy'); ?>">
                                    <?php echo e(ucfirst($offer->type)); ?>

                                </span>
                            </td>
                            <td class="font-mono">Bs. <?php echo e(number_format($offer->price, 2)); ?></td>
                            <td class="font-mono">$ <?php echo e(number_format($offer->amount, 2)); ?></td>
                            <td>
                                <span class="badge badge--<?php echo e($offer->status === 'activa' ? 'active' : 'closed'); ?>">
                                    <?php echo e(ucfirst($offer->status)); ?>

                                </span>
                            </td>
                            <td class="text-muted text-sm"><?php echo e(\Carbon\Carbon::parse($offer->createdAt)->diffForHumans()); ?></td>
                            <td>
                                <?php if($offer->status === 'activa'): ?>
                                    <form method="POST" action="<?php echo e(route('offers.close', $offer->id)); ?>"
                                          onsubmit="return confirm('¿Cerrar esta oferta?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="btn-link btn-link--danger">Cerrar</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('conv-btn').addEventListener('click', async () => {
    const amount = document.getElementById('conv-amount').value;
    const from   = document.getElementById('conv-from').value;
    const type   = document.getElementById('conv-type').value;

    const btn = document.getElementById('conv-btn');
    btn.textContent = '...';
    btn.disabled = true;

    try {
        const res = await fetch('/api/v1/exchange-rates/convert', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ amount: parseFloat(amount), from, rate_type: type }),
        });

        const data = await res.json();

        if (data.success) {
            const r = data.data;
            const toLabel = from === 'BOB' ? 'USD' : 'BOB';
            document.getElementById('conv-output').textContent =
                (from === 'BOB' ? '$ ' : 'Bs. ') + r.output.toFixed(2);
            document.getElementById('conv-direction').textContent =
                r.direction + ' · tipo ' + type;
            document.getElementById('conv-result').style.display = 'flex';
        } else {
            alert(data.message || 'Error al convertir');
        }
    } catch (e) {
        alert('Error de conexión');
    } finally {
        btn.textContent = 'Convertir';
        btn.disabled = false;
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\collatech\dolarapp\resources\views/dashboard/index.blade.php ENDPATH**/ ?>
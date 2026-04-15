<?php $__env->startSection('title', 'Mercado de divisas Bolivia'); ?>

<?php $__env->startSection('content'); ?>


<section class="hero">
    <div class="container hero__content">

        
        <div class="hero__text">
            <span class="hero__tag">Bolivia · Mercado de divisas</span>
            <h1 class="hero__title">
                El cambio más<br>transparente del país
            </h1>
            <p class="hero__subtitle">
                Consulta el dólar oficial, paralelo y librecambista en tiempo real.
                Publica tu oferta de compra o venta y conecta con personas de confianza.
            </p>
            <div class="hero__actions">
                <a href="<?php echo e(route('offers.index')); ?>" class="btn btn--primary btn--lg">Ver mercado</a>
                <?php if(auth()->guard()->guest()): ?>
                    <a href="<?php echo e(route('register')); ?>" class="btn btn--ghost btn--lg">Crear cuenta gratis</a>
                <?php endif; ?>
                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('offers.create')); ?>" class="btn btn--ghost btn--lg">+ Publicar oferta</a>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="hero__rates">
            <?php $__currentLoopData = $rates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="hero-rate-card">
                    <span class="hero-rate-card__type"><?php echo e(ucfirst($rate->type)); ?></span>
                    <div class="hero-rate-card__prices">
                        <div>
                            <span class="hero-rate-card__label">Compra</span>
                            <span class="hero-rate-card__value font-mono">Bs. <?php echo e(number_format($rate->buyPrice, 2)); ?></span>
                        </div>
                        <div class="hero-rate-card__divider"></div>
                        <div>
                            <span class="hero-rate-card__label">Venta</span>
                            <span class="hero-rate-card__value hero-rate-card__value--sell font-mono">Bs. <?php echo e(number_format($rate->sellPrice, 2)); ?></span>
                        </div>
                    </div>
                    <span class="hero-rate-card__updated">Fuente: <?php echo e($rate->source); ?></span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

    </div>
</section>


<div class="converter-strip">
    <div class="container converter-strip__inner">
        <span class="converter-strip__label">Convertir ahora:</span>
        <div class="converter-strip__inputs">
            <input type="number" id="s-amount" class="form-input" style="max-width:120px"
                   value="100" min="0.01" step="0.01" placeholder="Monto">
            <select id="s-from" class="form-input form-select" style="max-width:160px">
                <option value="BOB">Bolivianos (BOB)</option>
                <option value="USD">Dólares (USD)</option>
            </select>
            <select id="s-type" class="form-input form-select" style="max-width:160px">
                <option value="oficial">Oficial</option>
                <option value="paralelo" selected>Paralelo</option>
                <option value="librecambista">Librecambista</option>
            </select>
            <button id="s-btn" class="btn btn--primary">Convertir</button>
        </div>
        <div id="s-result" class="converter-strip__result" style="display:none">
            <strong id="s-output" class="font-mono"></strong>
            <span id="s-dir" style="opacity:.75;font-size:0.8rem"></span>
        </div>
    </div>
</div>


<section class="container" style="padding-top:2.5rem;padding-bottom:3rem">
    <div class="section-header">
        <h2 class="section-title">Últimas ofertas</h2>
        <a href="<?php echo e(route('offers.index')); ?>" class="link text-sm">
            Ver las <?php echo e($totalCount); ?> ofertas activas →
        </a>
    </div>

    <?php if($offers->isEmpty()): ?>
        <div class="empty-state">
            <p class="empty-state__text">Aún no hay ofertas. ¡Sé el primero en publicar!</p>
            <?php if(auth()->guard()->guest()): ?>
                <a href="<?php echo e(route('register')); ?>" class="btn btn--primary btn--sm">Registrarme y publicar</a>
            <?php endif; ?>
            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('offers.create')); ?>" class="btn btn--primary btn--sm">Publicar oferta</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="offers-grid">
            <?php $__currentLoopData = $offers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('components.offer-card', ['offer' => $offer], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php if($hasMore): ?>
            <div style="text-align:center;margin-top:2rem">
                <a href="<?php echo e(route('offers.index')); ?>" class="btn btn--ghost btn--lg">
                    Ver todas las ofertas del mercado
                </a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>


<section class="how-it-works">
    <div class="container">
        <h2 class="section-center-title" style="text-align:center;margin-bottom:2.5rem">¿Cómo funciona?</h2>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-card__num">1</div>
                <h3 class="step-card__title">Consulta el tipo de cambio</h3>
                <p class="step-card__desc">
                    Ve el precio oficial, paralelo y librecambista en tiempo real,
                    sin necesidad de crear una cuenta.
                </p>
            </div>
            <div class="step-card">
                <div class="step-card__num">2</div>
                <h3 class="step-card__title">Publica tu oferta</h3>
                <p class="step-card__desc">
                    Crea tu cuenta gratis y publica si quieres comprar o vender dólares,
                    con el precio y monto que prefieras.
                </p>
            </div>
            <div class="step-card">
                <div class="step-card__num">3</div>
                <h3 class="step-card__title">Contacta y cierra el trato</h3>
                <p class="step-card__desc">
                    Encuentra la oferta que te conviene y contacta directamente
                    al vendedor o comprador por teléfono o WhatsApp.
                </p>
            </div>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('s-btn').addEventListener('click', async () => {
    const amount = parseFloat(document.getElementById('s-amount').value);
    const from   = document.getElementById('s-from').value;
    const type   = document.getElementById('s-type').value;
    const btn    = document.getElementById('s-btn');

    if (!amount || amount <= 0) return;

    btn.textContent = '...';
    btn.disabled = true;

    try {
        const res  = await fetch('/api/v1/exchange-rates/convert', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ amount, from, rate_type: type }),
        });
        const data = await res.json();

        if (data.success) {
            const r       = data.data;
            const symbol  = from === 'BOB' ? '$ ' : 'Bs. ';
            document.getElementById('s-output').textContent = symbol + r.output.toFixed(2);
            document.getElementById('s-dir').textContent    = r.direction;
            document.getElementById('s-result').style.display = 'flex';
        }
    } catch(e) {}

    btn.textContent = 'Convertir';
    btn.disabled = false;
});

document.getElementById('s-amount').addEventListener('keypress', e => {
    if (e.key === 'Enter') document.getElementById('s-btn').click();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\collatech\dolarapp\resources\views/home.blade.php ENDPATH**/ ?>
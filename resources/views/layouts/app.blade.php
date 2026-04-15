<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DólarApp') — Mercado de Cambio Bolivia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>

{{-- ── Navbar ──────────────────────────────────────────────────── --}}
<nav class="navbar" id="navbar">
    <div class="container navbar__inner">
        <a href="{{ route('dashboard') }}" class="navbar__brand">
            <span class="brand-icon">$</span>
            <span class="brand-name">DólarApp</span>
        </a>

        <div class="navbar__links">
            <a href="{{ route('offers.index') }}" class="nav-link {{ request()->routeIs('offers.*') ? 'nav-link--active' : '' }}">
                Mercado
            </a>
            <a href="{{ route('exchange-rates.index') }}" class="nav-link {{ request()->routeIs('exchange-rates.*') ? 'nav-link--active' : '' }}">
                Tipos de cambio
            </a>

            @auth
                <a href="{{ route('chat.index') }}" class="nav-link {{ request()->routeIs('chat.*') ? 'nav-link--active' : '' }}">
                    Mensajes
                </a>
                <a href="{{ route('offers.create') }}" class="btn btn--primary btn--sm">
                    + Publicar oferta
                </a>
                <div class="user-menu">
                    <span class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <a href="{{ route('profile.index') }}" class="user-name nav-link">{{ auth()->user()->name }}</a>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline">
                        @csrf
                        <button type="submit" class="btn-link">Salir</button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn--ghost btn--sm">Ingresar</a>
                <a href="{{ route('register') }}" class="btn btn--primary btn--sm">Registrarse</a>
            @endauth
        </div>
    </div>
</nav>

{{-- ── Flash Messages ──────────────────────────────────────────── --}}
@if(session('success'))
    <div class="flash flash--success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="flash flash--error">{{ session('error') }}</div>
@endif

{{-- ── Contenido principal ─────────────────────────────────────── --}}
<main class="main">
    @yield('content')
</main>

{{-- ── Footer ──────────────────────────────────────────────────── --}}
<footer class="footer">
    <div class="container">
        <span>DólarApp &copy; {{ date('Y') }} — Mercado de divisas Bolivia</span>
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
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>

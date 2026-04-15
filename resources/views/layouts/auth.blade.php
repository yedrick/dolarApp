<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') — DólarApp</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="auth-body">

<div class="auth-wrapper">
    {{-- Logo --}}
    <a href="{{ route('offers.index') }}" class="auth-logo">
        <span class="brand-icon brand-icon--lg">$</span>
        <span class="brand-name brand-name--lg">DólarApp</span>
    </a>

    {{-- Ticker de tipos de cambio (decorativo) --}}
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

    {{-- Card del formulario --}}
    <div class="auth-card">
        @if(session('error'))
            <div class="flash flash--error" style="margin-bottom:1rem">{{ session('error') }}</div>
        @endif
        @yield('content')
    </div>
</div>

<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>

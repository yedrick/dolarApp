@extends('layouts.auth')
@section('title', 'Ingresar')

@section('content')
<h1 class="auth-title">Bienvenido de vuelta</h1>
<p class="auth-subtitle">Ingresa para publicar y gestionar tus ofertas.</p>

<form method="POST" action="{{ route('login') }}" class="form" novalidate>
    @csrf

    <div class="form-group">
        <label class="form-label" for="email">Correo electrónico</label>
        <input
            type="email"
            id="email"
            name="email"
            class="form-input @error('email') form-input--error @enderror"
            value="{{ old('email') }}"
            placeholder="tu@correo.com"
            autocomplete="email"
            autofocus
        >
        @error('email')
            <span class="form-error">{{ $message }}</span>
        @enderror
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
            class="form-input @error('password') form-input--error @enderror"
            placeholder="••••••••"
            autocomplete="current-password"
        >
        @error('password')
            <span class="form-error">{{ $message }}</span>
        @enderror
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
    <a href="{{ route('register') }}" class="link">Regístrate gratis</a>
</p>
@endsection

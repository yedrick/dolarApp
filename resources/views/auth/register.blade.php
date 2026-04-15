@extends('layouts.auth')
@section('title', 'Crear cuenta')

@section('content')
<h1 class="auth-title">Crear cuenta</h1>
<p class="auth-subtitle">Únete al mercado de cambio más confiable de Bolivia.</p>

<form method="POST" action="{{ route('register') }}" class="form" novalidate>
    @csrf

    <div class="form-group">
        <label class="form-label" for="name">Nombre completo</label>
        <input
            type="text"
            id="name"
            name="name"
            class="form-input @error('name') form-input--error @enderror"
            value="{{ old('name') }}"
            placeholder="Juan Pérez"
            autocomplete="name"
            autofocus
        >
        @error('name')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

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
        >
        @error('email')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="password">Contraseña</label>
        <input
            type="password"
            id="password"
            name="password"
            class="form-input @error('password') form-input--error @enderror"
            placeholder="Mínimo 8 caracteres"
            autocomplete="new-password"
        >
        @error('password')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="password_confirmation">Confirmar contraseña</label>
        <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            class="form-input"
            placeholder="Repite la contraseña"
            autocomplete="new-password"
        >
    </div>

    <button type="submit" class="btn btn--primary btn--block">
        Crear cuenta gratuita
    </button>
</form>

<p class="auth-footer-text">
    ¿Ya tienes cuenta?
    <a href="{{ route('login') }}" class="link">Ingresar</a>
</p>
@endsection

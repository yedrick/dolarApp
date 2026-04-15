@extends('layouts.app')
@section('title', 'Mi perfil')

@section('content')
<div class="container container--narrow">

    {{-- Encabezado de perfil --}}
    <div class="profile-header">
        <div class="profile-avatar">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <div>
            <p class="profile-name">{{ auth()->user()->name }}</p>
            <p class="profile-email">{{ auth()->user()->email }}</p>
            <div class="profile-meta">
                <div class="profile-stat">
                    <span class="profile-stat__val">{{ $activeCount }}</span>
                    <span class="profile-stat__label">Ofertas activas</span>
                </div>
                <div class="profile-stat">
                    <span class="profile-stat__val">{{ $totalCount }}</span>
                    <span class="profile-stat__label">Total publicadas</span>
                </div>
                <div class="profile-stat">
                    <span class="profile-stat__val">{{ \Carbon\Carbon::parse(auth()->user()->created_at)->format('M Y') }}</span>
                    <span class="profile-stat__label">Miembro desde</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Cambiar contraseña --}}
    <section class="section">
        <h2 class="section-title">Cambiar contraseña</h2>
        <div class="form-card">
            <form method="POST" action="{{ route('profile.password') }}" class="form" novalidate>
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label" for="current_password">Contraseña actual</label>
                    <input type="password" id="current_password" name="current_password"
                           class="form-input @error('current_password') form-input--error @enderror"
                           placeholder="••••••••">
                    @error('current_password')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_password">Nueva contraseña</label>
                    <input type="password" id="new_password" name="new_password"
                           class="form-input @error('new_password') form-input--error @enderror"
                           placeholder="Mínimo 8 caracteres">
                    @error('new_password')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
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

    {{-- Zona de peligro --}}
    <section class="section">
        <h2 class="section-title" style="color:var(--color-sell)">Zona de peligro</h2>
        <div class="form-card" style="border-color:var(--color-sell)">
            <p class="text-sm" style="color:var(--color-text-muted);margin-bottom:1rem">
                Una vez que elimines tu cuenta, se borrarán todos tus datos permanentemente.
                Esta acción no se puede deshacer.
            </p>
            <form method="POST" action="{{ route('profile.destroy') }}"
                  onsubmit="return confirm('¿Seguro que deseas eliminar tu cuenta? Esta acción no se puede deshacer.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn--ghost" style="border-color:var(--color-sell);color:var(--color-sell)">
                    Eliminar mi cuenta
                </button>
            </form>
        </div>
    </section>

</div>
@endsection

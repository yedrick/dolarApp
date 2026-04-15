@extends('layouts.app')
@section('title', 'Página no encontrada')

@section('content')
<div class="container" style="text-align:center;padding:6rem 1rem">
    <span class="font-mono" style="font-size:5rem;font-weight:500;color:var(--color-border);display:block;margin-bottom:1rem">404</span>
    <h1 class="page-title" style="margin-bottom:0.5rem">Página no encontrada</h1>
    <p style="color:var(--color-text-muted);margin-bottom:2rem">
        La oferta o página que buscas no existe o fue eliminada.
    </p>
    <a href="{{ route('home') }}" class="btn btn--primary">← Volver al inicio</a>
</div>
@endsection

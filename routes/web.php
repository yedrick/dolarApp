<?php

use Illuminate\Support\Facades\Route;
use App\Infrastructure\Http\Controllers\Web\AuthWebController;
use App\Infrastructure\Http\Controllers\Web\DashboardController;
use App\Infrastructure\Http\Controllers\Web\HomeController;
use App\Infrastructure\Http\Controllers\Web\OfferWebController;
use App\Infrastructure\Http\Controllers\Web\ExchangeRateWebController;
use App\Infrastructure\Http\Controllers\Web\ProfileWebController;
use App\Infrastructure\Http\Controllers\Web\ChatWebController;

/*
|--------------------------------------------------------------------------
| Web Routes — DólarApp
|--------------------------------------------------------------------------
|
| Rutas que renderizan vistas Blade.
| La autenticación usa sesión Laravel (Auth::login).
|
*/

// ── Inicio ────────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// ── Auth ──────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthWebController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthWebController::class, 'login']);
    Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthWebController::class, 'register']);
});

Route::post('/logout', [AuthWebController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ── Públicas ──────────────────────────────────────────────────────────────
Route::get('/offers',         [OfferWebController::class, 'index'])->name('offers.index');
Route::get('/exchange-rates', [ExchangeRateWebController::class, 'index'])->name('exchange-rates.index');

// ── Protegidas (requieren sesión) ─────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/dashboard',          [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/offers/create',      [OfferWebController::class, 'create'])->name('offers.create');
    Route::post('/offers',            [OfferWebController::class, 'store'])->name('offers.store');
    Route::patch('/offers/{id}/close',[OfferWebController::class, 'close'])->name('offers.close');
    // Perfil de usuario
    Route::get('/profile',            [ProfileWebController::class, 'index'])->name('profile.index');
    Route::put('/profile/password',   [ProfileWebController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile',         [ProfileWebController::class, 'destroy'])->name('profile.destroy');

    // Chat y Mensajes
    Route::get('/chat',               [ChatWebController::class, 'index'])->name('chat.index');
    Route::get('/chat/{id}',          [ChatWebController::class, 'show'])->name('chat.show');
    Route::post('/chat/start',        [ChatWebController::class, 'startConversation'])->name('chat.start');
    Route::post('/chat/{id}/send',    [ChatWebController::class, 'send'])->name('chat.send');
});

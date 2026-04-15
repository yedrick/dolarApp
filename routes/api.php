<?php

use Illuminate\Support\Facades\Route;
use App\Infrastructure\Http\Controllers\ExchangeRateController;
use App\Infrastructure\Http\Controllers\OfferController;
use App\Infrastructure\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes – DólarApp
|--------------------------------------------------------------------------
|
| Rutas públicas: tipos de cambio, conversión, listado de ofertas.
| Rutas protegidas (sanctum): publicar y gestionar ofertas.
|
*/

// ── Rutas públicas ────────────────────────────────────────────────────────────

Route::prefix('v1')->group(function () {

    // Autenticación
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);

    // Tipos de cambio (públicos)
    Route::prefix('exchange-rates')->group(function () {
        Route::get('/',        [ExchangeRateController::class, 'index']);
        Route::post('/convert',[ExchangeRateController::class, 'convert']);
    });

    // Ofertas públicas (solo lectura sin auth)
    Route::get('offers', [OfferController::class, 'index']);

    // ── Rutas protegidas ──────────────────────────────────────────────────────

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('logout', [AuthController::class, 'logout']);

        // Gestión de ofertas
        Route::prefix('offers')->group(function () {
            Route::post('/',        [OfferController::class, 'store']);
            Route::get('/my',       [OfferController::class, 'myOffers']);
            Route::delete('/{id}',  [OfferController::class, 'destroy']);
        });
    });
});

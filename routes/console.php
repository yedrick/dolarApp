<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Tareas programadas DólarApp
|--------------------------------------------------------------------------
*/

// Actualizar tipos de cambio desde API cada 30 minutos
Schedule::command('rates:fetch')->everyThirtyMinutes();

// Expirar ofertas con más de 72 horas cada día a medianoche
Schedule::command('offers:expire')->dailyAt('00:00');

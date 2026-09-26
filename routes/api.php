<?php

use App\Http\Controllers\SsoController;
use App\Models\Unit;
use Illuminate\Support\Facades\Route;

Route::post('/sso/verify', [SsoController::class, 'verify'])
    ->name('sso.verify');

Route::get('/diklit/unit-kuota', function () {
    return response()->json(Unit::with('prodiKuotas')->get());
});

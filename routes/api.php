<?php

use App\Models\Unit;
use Illuminate\Support\Facades\Route;

Route::get('/diklit/unit-kuota', function () {
    return response()->json(Unit::with('prodiKuotas')->get());
});

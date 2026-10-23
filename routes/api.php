<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AntennaController;
use App\Http\Controllers\Api\InterventionController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.token')->group(function () {
    Route::get('/antennas', AntennaController::class);
    Route::post('/antennas/{antennaId}/interventions', [InterventionController::class, 'createIntervention']);
    Route::patch('/interventions/{interventionId}', [InterventionController::class, 'closeIntervention']);
});

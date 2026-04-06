<?php

use App\Modules\Domain\Controllers\Api\DomainController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('domains', DomainController::class);

    Route::post('domains/{domain}/check-now', [DomainController::class, 'checkNow'])
        ->name('api.domains.check-now');

    Route::get('domains/{domain}/logs', [DomainController::class, 'logs'])
        ->name('api.domains.logs');
});

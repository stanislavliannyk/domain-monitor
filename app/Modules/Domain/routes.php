<?php

use App\Modules\Domain\Controllers\DomainController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('domains', DomainController::class);

    Route::post('domains/{domain}/check-now', [DomainController::class, 'checkNow'])
        ->name('domains.check-now');
});

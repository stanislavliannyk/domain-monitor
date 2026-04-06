<?php

use App\Modules\Dashboard\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('api.dashboard');
});

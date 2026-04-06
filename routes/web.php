<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| The entire frontend is a Vue 3 SPA. This single catch-all route serves
| the app shell; Vue Router takes over all client-side navigation.
|--------------------------------------------------------------------------
*/

Route::get('/{any}', fn () => view('app'))->where('any', '.*');

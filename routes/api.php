<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::post('/contact', \App\Http\Controllers\ContactController::class)
    ->middleware('throttle:5,1');

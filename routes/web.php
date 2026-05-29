<?php

use Illuminate\Support\Facades\Route;

// All web routes are handled by the SPA (single entry point)
Route::view('/{any}', 'app')
    ->where('any', '^(?!api|sanctum|_boost|login|register|logout|forgot-password|reset-password|two-factor|email|user|passkeys).*$')
    ->name('app');

require __DIR__.'/settings.php';

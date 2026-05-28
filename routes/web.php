<?php

use Illuminate\Support\Facades\Route;

// All web routes are handled by the SPA (single entry point)
Route::view('/{any}', 'app')
    ->where('any', '.*')
    ->name('app');

require __DIR__.'/settings.php';


<?php

use App\Http\Controllers\Tenant\Settings\PasswordController;
use App\Http\Controllers\Tenant\Settings\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('tenant.settings.profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('tenant.settings.profile.destroy');

    Route::put('settings/password', [PasswordController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('tenant.settings.password.update');
});

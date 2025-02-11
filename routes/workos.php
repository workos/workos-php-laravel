<?php

use App\Http\Controllers\WorkOS\WorkOSAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get('/login', function () {
        return view('workos::login');
    })->name('login');

    Route::get('/auth/workos', [WorkOSAuthController::class, 'redirect'])->name('workos.redirect');

    Route::get('/auth/workos/callback', [WorkOSAuthController::class, 'callback'])->name('workos.callback');

    Route::get('/logout', [WorkOSAuthController::class, 'logout'])->name('workos.logout');

    Route::get('/dashboard', function () {
        return 'You are logged in!';
    })->name('workos.dashboard')->middleware(('auth'));
});

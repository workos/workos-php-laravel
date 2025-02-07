<?php

use App\Http\Controllers\WorkOS\WorkOSAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
	Route::get('/auth/workos', [WorkOSAuthController::class, 'redirect'])->name('workos.redirect');
	Route::get('/auth/workos/callback', [WorkOSAuthController::class, 'callback'])->name('workos.callback');
});

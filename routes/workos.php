<?php

use App\Http\Controllers\WorkOS\WorkOSAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
	Route::get('/login/workos', function() {
		return view('workos::login');
	})->name('workos.login');

	Route::get('/auth/workos', [WorkOSAuthController::class, 'redirect'])->name('workos.redirect');

	Route::get('/auth/workos/callback', [WorkOSAuthController::class, 'callback'])->name('workos.callback');

	Route::get('/auth/workos/logout', [WorkOSAuthController::class, 'logout'])->name('workos.logout');
});

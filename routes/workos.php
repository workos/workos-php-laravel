<?php

use App\Http\Controllers\WorkOS\WorkOSAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
	Route::get('/auth/workos/login', function() {
		return view('workos::login');
	})->name('login');

	Route::get('/auth/workos', [WorkOSAuthController::class, 'redirect'])->name('workos.redirect');

	Route::get('/auth/workos/callback', [WorkOSAuthController::class, 'callback'])->name('workos.callback');

	Route::get('/auth/workos/logout', [WorkOSAuthController::class, 'logout'])->name('workos.logout');

	Route::get('/auth/workos/dashboard', function() {
		return 'You are logged in!';
	})->name('workos.dashboard')->middleware(('auth'));
});

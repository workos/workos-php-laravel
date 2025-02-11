<?php

use App\Http\Controllers\WorkOS\WorkOSAuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::middleware('web')->group(function () {
    Route::get('/login', function () {
        return view('workos::login');
    })->name('login');

    Route::get('/auth/workos', function () {
        return Socialite::driver('workos')->redirect();
    });

    Route::get('/auth/workos/callback', function () {
        $user = Socialite::driver('workos')->user();

        $userModel = config('auth.providers.users.model');

        // Find or create user
        $user = $userModel::firstOrCreate(
            ['email' => $user->email],
            ['name' => $user->name]
        );

        Auth::login($user);

        return redirect('/dashboard');
    })->name('workos.callback');

    Route::get('/logout', [WorkOSAuthController::class, 'logout'])->name('workos.logout');

    Route::get('/dashboard', function () {
        return 'You are logged in!';
    })->name('workos.dashboard')->middleware(('auth'));
});

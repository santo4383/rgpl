<?php

use App\Http\Controllers\Auth\OtpLoginController;
use App\Http\Controllers\Auth\OtpRegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [OtpRegisterController::class, 'create'])->name('register');
    Route::post('register', [OtpRegisterController::class, 'store'])->name('register.store');
    Route::post('register/send-otp', [OtpRegisterController::class, 'sendOtp'])->name('register.send-otp');
    Route::post('register/verify', [OtpRegisterController::class, 'verifyOtpAndRegister'])->name('register.verify');

    Route::get('login', [OtpLoginController::class, 'create'])->name('login');
    Route::post('login', [OtpLoginController::class, 'store'])->name('login.store');
    Route::post('login/send-otp', [OtpLoginController::class, 'sendOtp'])->name('login.send-otp');
    Route::post('login/verify', [OtpLoginController::class, 'verifyOtp'])->name('login.verify');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    })->name('logout');
});

<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\MerchantAuthController;
use App\Http\Controllers\Api\PublicOtpController;
use Illuminate\Support\Facades\Route;

Route::post('/public/otp/{token}', [PublicOtpController::class, 'generate'])
    ->middleware('throttle:otp-public')
    ->name('api.public.otp.generate');

Route::post('/public/otp', [PublicOtpController::class, 'generateFromRequest'])
    ->middleware('throttle:otp-public')
    ->name('api.public.otp.generate.query');

Route::post('/merchant/login', [MerchantAuthController::class, 'login'])->name('api.merchant.login');

Route::middleware(['auth:sanctum', 'merchant'])->prefix('merchant')->group(function () {
    Route::post('/logout', [MerchantAuthController::class, 'logout'])->name('api.merchant.logout');
    Route::get('/me', [MerchantAuthController::class, 'me'])->name('api.merchant.me');

    Route::get('/accounts', [AccountController::class, 'index'])->name('api.merchant.accounts.index');
    Route::post('/accounts', [AccountController::class, 'store'])->name('api.merchant.accounts.store');
    Route::put('/accounts/{accountId}', [AccountController::class, 'update'])->name('api.merchant.accounts.update');
    Route::delete('/accounts/{accountId}', [AccountController::class, 'destroy'])->name('api.merchant.accounts.destroy');

    Route::get('/customers', [CustomerController::class, 'index'])->name('api.merchant.customers.index');
    Route::post('/customers', [CustomerController::class, 'store'])->name('api.merchant.customers.store');
    Route::post('/customers/bulk', [CustomerController::class, 'bulkStore'])->name('api.merchant.customers.bulk');
    Route::put('/customers/{customerId}', [CustomerController::class, 'update'])->name('api.merchant.customers.update');
    Route::post('/customers/{customerId}/renew', [CustomerController::class, 'renewBalance'])->name('api.merchant.customers.renew');
    Route::post('/customers/{customerId}/regenerate-token', [CustomerController::class, 'regenerateToken'])->name('api.merchant.customers.regenerate-token');
    Route::delete('/customers/{customerId}', [CustomerController::class, 'destroy'])->name('api.merchant.customers.destroy');
});

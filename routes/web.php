<?php

use App\Http\Controllers\Admin\AdminPortalController;
use App\Http\Controllers\PublicInfoController;
use App\Http\Controllers\Web\MerchantPortalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', '/merchant/login');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminPortalController::class, 'showLoginForm'])->name('login.form');
        Route::post('/login', [AdminPortalController::class, 'login'])->name('login');
        Route::get('/forgot-password', [AdminPortalController::class, 'showForgotPasswordForm'])->name('forgot-password.form');
        Route::post('/forgot-password', [AdminPortalController::class, 'sendOtp'])->name('forgot-password.send-otp');
        Route::get('/forgot-password/verify', [AdminPortalController::class, 'showVerifyOtpForm'])->name('forgot-password.verify.form');
        Route::post('/forgot-password/verify', [AdminPortalController::class, 'verifyOtpAndReset'])->name('forgot-password.verify');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/logout', [AdminPortalController::class, 'logout'])->name('logout');

        Route::get('/dashboard', [AdminPortalController::class, 'dashboard'])->name('dashboard');

        Route::get('/merchants', [AdminPortalController::class, 'merchants'])->name('merchants.index');
        Route::post('/merchants', [AdminPortalController::class, 'storeMerchant'])->name('merchants.store');
        Route::get('/merchants/{merchantId}/edit', [AdminPortalController::class, 'editMerchant'])->name('merchants.edit');
        Route::put('/merchants/{merchantId}', [AdminPortalController::class, 'updateMerchant'])->name('merchants.update');
        Route::post('/merchants/{merchantId}/toggle', [AdminPortalController::class, 'toggleMerchant'])->name('merchants.toggle');
        Route::delete('/merchants/{merchantId}', [AdminPortalController::class, 'deleteMerchant'])->name('merchants.delete');
    });
});

Route::prefix('merchant')->name('merchant.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [MerchantPortalController::class, 'showLoginForm'])->name('login.form');
        Route::post('/login', [MerchantPortalController::class, 'login'])->name('login');
        Route::get('/forgot-password', [MerchantPortalController::class, 'showForgotPasswordForm'])->name('forgot-password.form');
        Route::post('/forgot-password', [MerchantPortalController::class, 'sendOtp'])->name('forgot-password.send-otp');
        Route::get('/forgot-password/verify', [MerchantPortalController::class, 'showVerifyOtpForm'])->name('forgot-password.verify.form');
        Route::post('/forgot-password/verify', [MerchantPortalController::class, 'verifyOtpAndReset'])->name('forgot-password.verify');
    });

    Route::middleware(['auth', 'merchant'])->group(function () {
        Route::post('/logout', [MerchantPortalController::class, 'logout'])->name('logout');

        Route::get('/profile', [MerchantPortalController::class, 'showProfileForm'])->name('profile.edit');
        Route::put('/profile', [MerchantPortalController::class, 'updateProfile'])->name('profile.update');

        Route::get('/dashboard', [MerchantPortalController::class, 'dashboard'])->name('dashboard');

        Route::get('/accounts', [MerchantPortalController::class, 'accounts'])->name('accounts.index');
        Route::get('/accounts/create', [MerchantPortalController::class, 'showAccountCreateForm'])->name('accounts.create');
        Route::post('/accounts', [MerchantPortalController::class, 'storeAccount'])->name('accounts.store');
        Route::get('/accounts/{accountId}/edit', [MerchantPortalController::class, 'editAccount'])->name('accounts.edit');
        Route::put('/accounts/{accountId}', [MerchantPortalController::class, 'updateAccount'])->name('accounts.update');
        Route::delete('/accounts/{accountId}', [MerchantPortalController::class, 'deleteAccount'])->name('accounts.delete');

        Route::get('/customers', [MerchantPortalController::class, 'customers'])->name('customers.index');
        Route::get('/customers/create', [MerchantPortalController::class, 'showCustomerCreateForm'])->name('customers.create');
        Route::get('/customers/bulk', [MerchantPortalController::class, 'showBulkCreateForm'])->name('customers.bulk');
        Route::post('/customers', [MerchantPortalController::class, 'storeCustomer'])->name('customers.store');
        Route::post('/customers/bulk', [MerchantPortalController::class, 'storeCustomersBulk'])->name('customers.store-bulk');
        Route::get('/customers/{customerId}/edit', [MerchantPortalController::class, 'showCustomerEditForm'])->name('customers.edit');
        Route::put('/customers/{customerId}', [MerchantPortalController::class, 'updateCustomer'])->name('customers.update');
        Route::post('/customers/{customerId}/renew', [MerchantPortalController::class, 'renewCustomerBalance'])->name('customers.renew');
        Route::post('/customers/{customerId}/regenerate-token', [MerchantPortalController::class, 'regenerateCustomerToken'])->name('customers.regenerate-token');
        Route::delete('/customers/{customerId}', [MerchantPortalController::class, 'deleteCustomer'])->name('customers.delete');
    });
});

Route::get('/info/{token}', [PublicInfoController::class, 'show'])->name('public.info');

// Sitemap
use App\Http\Controllers\SitemapController;
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

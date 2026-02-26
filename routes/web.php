<?php

use App\Http\Controllers\Website\CheckoutViewController;
use App\Http\Controllers\Website\DashboardController;
use App\Http\Controllers\Website\PlanViewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('website.plans.index');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('website.dashboard.subscriptions');
    })->name('dashboard');
});

// Website routes
Route::prefix('/')->name('website.')->group(function () {
    Route::get('/plans', [PlanViewController::class, 'index'])->name('plans.index');
    Route::get('/plan/{slug}', [PlanViewController::class, 'show'])->name('plan.show');
    // Checkout page
    Route::get('/checkout/{plan_id}', [CheckoutViewController::class, 'index'])->name('checkout.index');
});

Route::middleware(['auth'])->prefix('dashboard')
    ->group(function () {
        Route::get('/subscriptions', [DashboardController::class, 'subscriptions'])->name('website.dashboard.subscriptions');
        Route::get('/invoices', [DashboardController::class, 'invoices'])->name('website.dashboard.invoices');
        Route::get('/payment-methods', [DashboardController::class, 'paymentMethods'])->name('website.dashboard.payment-methods');
        Route::get('/usage', [DashboardController::class, 'usage'])->name('website.dashboard.usage');
        Route::get('/profile', [DashboardController::class, 'profile'])->name('website.dashboard.profile');
        Route::get('/settings', [DashboardController::class, 'settings'])->name('website.dashboard.settings');
    });

// SSLCommerz Payment Routes
Route::prefix('payment/sslcommerz')->name('payment.sslcommerz.')->group(function () {
    // GET requests from browser - Include session to keep user logged in
    Route::middleware(['payment'])->group(function () {
        Route::get('success', [App\Http\Controllers\Api\V1\CheckoutController::class, 'handleCallback'])->name('success')->defaults('gateway', 'sslcommerz');
        Route::get('fail', [App\Http\Controllers\Api\V1\CheckoutController::class, 'handleCallback'])->name('fail')->defaults('gateway', 'sslcommerz');
        Route::get('cancel', [App\Http\Controllers\Api\V1\CheckoutController::class, 'handleCallback'])->name('cancel')->defaults('gateway', 'sslcommerz');
    });

    // POST callbacks from gateway - Exclude session to prevent cookie overwriting/logout
    Route::middleware(['web'])
        ->withoutMiddleware([
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        ])
        ->group(function () {
            Route::post('success', [App\Http\Controllers\Api\V1\CheckoutController::class, 'handleCallback'])->defaults('gateway', 'sslcommerz');
            Route::post('fail', [App\Http\Controllers\Api\V1\CheckoutController::class, 'handleCallback'])->defaults('gateway', 'sslcommerz');
            Route::post('cancel', [App\Http\Controllers\Api\V1\CheckoutController::class, 'handleCallback'])->defaults('gateway', 'sslcommerz');
            Route::post('ipn', [App\Http\Controllers\Api\V1\CheckoutController::class, 'handleCallback'])->name('ipn')->defaults('gateway', 'sslcommerz');
        });
});

// Nagad Payment Routes
Route::prefix('payment/nagad')->name('payment.nagad.')->group(function () {
    Route::middleware(['payment'])->group(function () {
        Route::get('success', [App\Http\Controllers\Api\V1\CheckoutController::class, 'handleCallback'])->name('success')->defaults('gateway', 'nagad');
        Route::get('fail', [App\Http\Controllers\Api\V1\CheckoutController::class, 'handleCallback'])->name('fail')->defaults('gateway', 'nagad');
    });

    Route::middleware(['web'])
        ->withoutMiddleware([
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        ])
        ->group(function () {
            Route::post('callback', [App\Http\Controllers\Api\V1\CheckoutController::class, 'handleCallback'])->name('callback')->defaults('gateway', 'nagad');
        });
});

// Rocket Payment Routes
Route::prefix('payment/rocket')->name('payment.rocket.')->group(function () {
    Route::middleware(['payment'])->group(function () {
        Route::get('success', [App\Http\Controllers\Api\V1\CheckoutController::class, 'handleCallback'])->name('success')->defaults('gateway', 'rocket');
        Route::get('fail', [App\Http\Controllers\Api\V1\CheckoutController::class, 'handleCallback'])->name('fail')->defaults('gateway', 'rocket');
    });

    Route::middleware(['web'])
        ->withoutMiddleware([
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        ])
        ->group(function () {
            Route::post('callback', [App\Http\Controllers\Api\V1\CheckoutController::class, 'handleCallback'])->name('callback')->defaults('gateway', 'rocket');
            Route::post('ipn', [App\Http\Controllers\Api\V1\CheckoutController::class, 'handleCallback'])->name('ipn')->defaults('gateway', 'rocket');
        });
});

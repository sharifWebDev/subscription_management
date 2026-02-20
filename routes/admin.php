<?php

// routes/admin.php
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

// path1

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'), 'verified',
])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard']);
    Route::get('/artisan/optimize', [AdminController::class, 'optimize'])->name('artisan.optimize');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::post('/toggle-status/{id}', [AdminController::class, 'toggleStatus'])->name('data.toggleStatus');

    // /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    Route::prefix('/hk-prod-uoms')
        ->controller(\App\Http\Controllers\HkProdUomController::class)
        ->group(function () {
            Route::get('/', 'index')->name('hk-prod-uoms.index');
            Route::get('/create', 'create')->name('hk-prod-uoms.create');
            Route::get('/{id}/edit', 'edit')->name('hk-prod-uoms.edit');
            Route::get('/{id}/show', 'show')->name('hk-prod-uoms.show');
        });
    // /////////////////////////////////

    Route::prefix('/discounts')
        ->controller(\App\Http\Controllers\DiscountController::class)
        ->group(function () {
            Route::get('/', 'index')->name('discounts.index');
            Route::get('/create', 'create')->name('discounts.create');
            Route::get('/{id}/edit', 'edit')->name('discounts.edit');
            Route::get('/{id}/show', 'show')->name('discounts.show');
        });
    // /////////////////////////////////

    Route::prefix('/features')
        ->controller(\App\Http\Controllers\FeatureController::class)
        ->group(function () {
            Route::get('/', 'index')->name('features.index');
            Route::get('/create', 'create')->name('features.create');
            Route::get('/{id}/edit', 'edit')->name('features.edit');
            Route::get('/{id}/show', 'show')->name('features.show');
        });
    // /////////////////////////////////

    Route::prefix('/invoices')
        ->controller(\App\Http\Controllers\InvoiceController::class)
        ->group(function () {
            Route::get('/', 'index')->name('invoices.index');
            Route::get('/create', 'create')->name('invoices.create');
            Route::get('/{id}/edit', 'edit')->name('invoices.edit');
            Route::get('/{id}/show', 'show')->name('invoices.show');
        });
    // /////////////////////////////////

    Route::prefix('/invoices')
        ->controller(\App\Http\Controllers\InvoiceController::class)
        ->group(function () {
            Route::get('/', 'index')->name('invoices.index');
            Route::get('/create', 'create')->name('invoices.create');
            Route::get('/{id}/edit', 'edit')->name('invoices.edit');
            Route::get('/{id}/show', 'show')->name('invoices.show');
        });
    // /////////////////////////////////

    Route::prefix('/invoices')
        ->controller(\App\Http\Controllers\InvoiceController::class)
        ->group(function () {
            Route::get('/', 'index')->name('invoices.index');
            Route::get('/create', 'create')->name('invoices.create');
            Route::get('/{id}/edit', 'edit')->name('invoices.edit');
            Route::get('/{id}/show', 'show')->name('invoices.show');
        });
    // /////////////////////////////////

    Route::prefix('/metered-usage-aggregates')
        ->controller(\App\Http\Controllers\MeteredUsageAggregateController::class)
        ->group(function () {
            Route::get('/', 'index')->name('metered-usage-aggregates.index');
            Route::get('/create', 'create')->name('metered-usage-aggregates.create');
            Route::get('/{id}/edit', 'edit')->name('metered-usage-aggregates.edit');
            Route::get('/{id}/show', 'show')->name('metered-usage-aggregates.show');
        });
    // /////////////////////////////////

    Route::prefix('/payment-allocations')
        ->controller(\App\Http\Controllers\PaymentAllocationController::class)
        ->group(function () {
            Route::get('/', 'index')->name('payment-allocations.index');
            Route::get('/create', 'create')->name('payment-allocations.create');
            Route::get('/{id}/edit', 'edit')->name('payment-allocations.edit');
            Route::get('/{id}/show', 'show')->name('payment-allocations.show');
        });
    // /////////////////////////////////

    Route::prefix('/payment-children')
        ->controller(\App\Http\Controllers\PaymentChildController::class)
        ->group(function () {
            Route::get('/', 'index')->name('payment-children.index');
            Route::get('/create', 'create')->name('payment-children.create');
            Route::get('/{id}/edit', 'edit')->name('payment-children.edit');
            Route::get('/{id}/show', 'show')->name('payment-children.show');
        });
    // /////////////////////////////////

    Route::prefix('/payment-gateways')
        ->controller(\App\Http\Controllers\PaymentGatewayController::class)
        ->group(function () {
            Route::get('/', 'index')->name('payment-gateways.index');
            Route::get('/create', 'create')->name('payment-gateways.create');
            Route::get('/{id}/edit', 'edit')->name('payment-gateways.edit');
            Route::get('/{id}/show', 'show')->name('payment-gateways.show');
        });
    // /////////////////////////////////

    Route::prefix('/payment-masters')
        ->controller(\App\Http\Controllers\PaymentMasterController::class)
        ->group(function () {
            Route::get('/', 'index')->name('payment-masters.index');
            Route::get('/create', 'create')->name('payment-masters.create');
            Route::get('/{id}/edit', 'edit')->name('payment-masters.edit');
            Route::get('/{id}/show', 'show')->name('payment-masters.show');
        });
    // /////////////////////////////////

    Route::prefix('/payment-methods')
        ->controller(\App\Http\Controllers\PaymentMethodController::class)
        ->group(function () {
            Route::get('/', 'index')->name('payment-methods.index');
            Route::get('/create', 'create')->name('payment-methods.create');
            Route::get('/{id}/edit', 'edit')->name('payment-methods.edit');
            Route::get('/{id}/show', 'show')->name('payment-methods.show');
        });
    // /////////////////////////////////

    Route::prefix('/payment-transactions')
        ->controller(\App\Http\Controllers\PaymentTransactionController::class)
        ->group(function () {
            Route::get('/', 'index')->name('payment-transactions.index');
            Route::get('/create', 'create')->name('payment-transactions.create');
            Route::get('/{id}/edit', 'edit')->name('payment-transactions.edit');
            Route::get('/{id}/show', 'show')->name('payment-transactions.show');
        });
    // /////////////////////////////////

    Route::prefix('/payment-webhook-logs')
        ->controller(\App\Http\Controllers\PaymentWebhookLogController::class)
        ->group(function () {
            Route::get('/', 'index')->name('payment-webhook-logs.index');
            Route::get('/create', 'create')->name('payment-webhook-logs.create');
            Route::get('/{id}/edit', 'edit')->name('payment-webhook-logs.edit');
            Route::get('/{id}/show', 'show')->name('payment-webhook-logs.show');
        });
    // /////////////////////////////////

    Route::prefix('/payments')
        ->controller(\App\Http\Controllers\PaymentController::class)
        ->group(function () {
            Route::get('/', 'index')->name('payments.index');
            Route::get('/create', 'create')->name('payments.create');
            Route::get('/{id}/edit', 'edit')->name('payments.edit');
            Route::get('/{id}/show', 'show')->name('payments.show');
        });
    // /////////////////////////////////

    Route::prefix('/plan-features')
        ->controller(\App\Http\Controllers\PlanFeatureController::class)
        ->group(function () {
            Route::get('/', 'index')->name('plan-features.index');
            Route::get('/create', 'create')->name('plan-features.create');
            Route::get('/{id}/edit', 'edit')->name('plan-features.edit');
            Route::get('/{id}/show', 'show')->name('plan-features.show');
        });
    // /////////////////////////////////

    Route::prefix('/plan-prices')
        ->controller(\App\Http\Controllers\PlanPriceController::class)
        ->group(function () {
            Route::get('/', 'index')->name('plan-prices.index');
            Route::get('/create', 'create')->name('plan-prices.create');
            Route::get('/{id}/edit', 'edit')->name('plan-prices.edit');
            Route::get('/{id}/show', 'show')->name('plan-prices.show');
        });
    // /////////////////////////////////

    Route::prefix('/plans')
        ->controller(\App\Http\Controllers\PlanController::class)
        ->group(function () {
            Route::get('/', 'index')->name('plans.index');
            Route::get('/create', 'create')->name('plans.create');
            Route::get('/{id}/edit', 'edit')->name('plans.edit');
            Route::get('/{id}/show', 'show')->name('plans.show');
        });
    // /////////////////////////////////

    Route::prefix('/rate-limits')
        ->controller(\App\Http\Controllers\RateLimitController::class)
        ->group(function () {
            Route::get('/', 'index')->name('rate-limits.index');
            Route::get('/create', 'create')->name('rate-limits.create');
            Route::get('/{id}/edit', 'edit')->name('rate-limits.edit');
            Route::get('/{id}/show', 'show')->name('rate-limits.show');
        });
    // /////////////////////////////////

    Route::prefix('/refunds')
        ->controller(\App\Http\Controllers\RefundController::class)
        ->group(function () {
            Route::get('/', 'index')->name('refunds.index');
            Route::get('/create', 'create')->name('refunds.create');
            Route::get('/{id}/edit', 'edit')->name('refunds.edit');
            Route::get('/{id}/show', 'show')->name('refunds.show');
        });
    // /////////////////////////////////

    Route::prefix('/subscription-events')
        ->controller(\App\Http\Controllers\SubscriptionEventController::class)
        ->group(function () {
            Route::get('/', 'index')->name('subscription-events.index');
            Route::get('/create', 'create')->name('subscription-events.create');
            Route::get('/{id}/edit', 'edit')->name('subscription-events.edit');
            Route::get('/{id}/show', 'show')->name('subscription-events.show');
        });
    // /////////////////////////////////

    Route::prefix('/subscription-items')
        ->controller(\App\Http\Controllers\SubscriptionItemController::class)
        ->group(function () {
            Route::get('/', 'index')->name('subscription-items.index');
            Route::get('/create', 'create')->name('subscription-items.create');
            Route::get('/{id}/edit', 'edit')->name('subscription-items.edit');
            Route::get('/{id}/show', 'show')->name('subscription-items.show');
        });
    // /////////////////////////////////

    Route::prefix('/subscription-order-items')
        ->controller(\App\Http\Controllers\SubscriptionOrderItemController::class)
        ->group(function () {
            Route::get('/', 'index')->name('subscription-order-items.index');
            Route::get('/create', 'create')->name('subscription-order-items.create');
            Route::get('/{id}/edit', 'edit')->name('subscription-order-items.edit');
            Route::get('/{id}/show', 'show')->name('subscription-order-items.show');
        });
    // /////////////////////////////////

    Route::prefix('/subscription-orders')
        ->controller(\App\Http\Controllers\SubscriptionOrderController::class)
        ->group(function () {
            Route::get('/', 'index')->name('subscription-orders.index');
            Route::get('/create', 'create')->name('subscription-orders.create');
            Route::get('/{id}/edit', 'edit')->name('subscription-orders.edit');
            Route::get('/{id}/show', 'show')->name('subscription-orders.show');
        });
    // /////////////////////////////////

    Route::prefix('/subscriptions')
        ->controller(\App\Http\Controllers\SubscriptionController::class)
        ->group(function () {
            Route::get('/', 'index')->name('subscriptions.index');
            Route::get('/create', 'create')->name('subscriptions.create');
            Route::get('/{id}/edit', 'edit')->name('subscriptions.edit');
            Route::get('/{id}/show', 'show')->name('subscriptions.show');
        });
    // /////////////////////////////////

    Route::prefix('/usage-records')
        ->controller(\App\Http\Controllers\UsageRecordController::class)
        ->group(function () {
            Route::get('/', 'index')->name('usage-records.index');
            Route::get('/create', 'create')->name('usage-records.create');
            Route::get('/{id}/edit', 'edit')->name('usage-records.edit');
            Route::get('/{id}/show', 'show')->name('usage-records.show');
        });
    // /////////////////////////////////

    Route::prefix('/refunds')
        ->controller(\App\Http\Controllers\RefundController::class)
        ->group(function () {
            Route::get('/', 'index')->name('refunds.index');
            Route::get('/create', 'create')->name('refunds.create');
            Route::get('/{id}/edit', 'edit')->name('refunds.edit');
            Route::get('/{id}/show', 'show')->name('refunds.show');
        });
    // /////////////////////////////////

    Route::prefix('/invoices')
        ->controller(\App\Http\Controllers\InvoiceController::class)
        ->group(function () {
            Route::get('/', 'index')->name('invoices.index');
            Route::get('/create', 'create')->name('invoices.create');
            Route::get('/{id}/edit', 'edit')->name('invoices.edit');
            Route::get('/{id}/show', 'show')->name('invoices.show');
        });
    // /////////////////////////////////

    // path3

});

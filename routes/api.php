<?php

use App\Http\Controllers\Api\V1\CheckoutController;
use App\Http\Controllers\Api\V1\DiscountController;
use App\Http\Controllers\Api\V1\FeatureController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\MeteredUsageAggregateController;
use App\Http\Controllers\Api\V1\PaymentAllocationController;
use App\Http\Controllers\Api\V1\PaymentChildController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\PaymentGatewayController;
use App\Http\Controllers\Api\V1\PaymentMasterController;
use App\Http\Controllers\Api\V1\PaymentMethodController;
use App\Http\Controllers\Api\V1\PaymentTransactionController;
use App\Http\Controllers\Api\V1\PaymentWebhookLogController;
use App\Http\Controllers\Api\V1\PlanController;
use App\Http\Controllers\Api\V1\PlanFeatureController;
use App\Http\Controllers\Api\V1\PlanPriceController;
use App\Http\Controllers\Api\V1\RateLimitController;
use App\Http\Controllers\Api\V1\RefundController;
use App\Http\Controllers\Api\V1\SubscriptionController;
use App\Http\Controllers\Api\V1\SubscriptionEventController;
use App\Http\Controllers\Api\V1\SubscriptionItemController;
use App\Http\Controllers\Api\V1\SubscriptionOrderController;
use App\Http\Controllers\Api\V1\SubscriptionOrderItemController;
use App\Http\Controllers\Api\V1\UsageController;
use App\Http\Controllers\Api\V1\UsageRecordController;
use App\Http\Controllers\Api\V1\WebhookController;
use App\Http\Controllers\CrudGeneratorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

Route::middleware('auth:sanctum')->get('/v1/user', function (Request $request) {
    return '$request->user()';
});

Route::middleware('auth:sanctum', 'verified')
    ->prefix('/v1')
    ->group(function () {
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);

        Route::middleware('auth:sanctum')
            ->controller(PlanController::class)
            ->prefix('plans')
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'find');
                Route::put('/update/{id}', 'update');
                Route::delete('/destroy/{id}', 'destroy');
            });

        Route::middleware('auth:sanctum')
            ->controller(SubscriptionController::class)
            ->prefix('subscriptions')
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'find');
                Route::put('/update/{id}', 'update');
                Route::delete('/destroy/{id}', 'destroy');
            });

        // refunds

        Route::middleware('auth:sanctum')
            ->controller(RefundController::class)
            ->prefix('refunds')
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'find');
                Route::put('/update/{id}', 'update');
                Route::delete('/destroy/{id}', 'destroy');
            });

        Route::prefix('/discounts')
            ->controller(DiscountController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'find');
                Route::put('/update/{id}', 'update');
                Route::delete('/destroy/{id}', 'destroy');
            });

        Route::prefix('/features')
            ->controller(FeatureController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'find');
                Route::put('/update/{id}', 'update');
                Route::delete('/destroy/{id}', 'destroy');
            });

        Route::prefix('/invoices')
            ->controller(InvoiceController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'find');
                Route::put('/update/{id}', 'update');
                Route::delete('/destroy/{id}', 'destroy');
            });

        Route::prefix('/metered-usage-aggregates')
            ->controller(MeteredUsageAggregateController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'find');
                Route::put('/update/{id}', 'update');
                Route::delete('/destroy/{id}', 'destroy');
            });

        Route::prefix('/payment-allocations')
            ->controller(PaymentAllocationController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'find');
                Route::put('/update/{id}', 'update');
                Route::delete('/destroy/{id}', 'destroy');
            });

        Route::prefix('/payment-children')
            ->controller(PaymentChildController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'find');
                Route::put('/update/{id}', 'update');
                Route::delete('/destroy/{id}', 'destroy');
            });

        Route::prefix('/payment-gateways')
            ->controller(PaymentGatewayController::class)
            ->group(function () {
                // Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'find');
                Route::put('/update/{id}', 'update');
                Route::delete('/destroy/{id}', 'destroy');
            });

        Route::prefix('/payment-masters')
            ->controller(PaymentMasterController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'find');
                Route::put('/update/{id}', 'update');
                Route::delete('/destroy/{id}', 'destroy');
            });

        Route::prefix('/payment-methods')
            ->controller(PaymentMethodController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'find');
                Route::put('/update/{id}', 'update');
                Route::delete('/destroy/{id}', 'destroy');
            });

        Route::prefix('/payment-transactions')
            ->controller(PaymentTransactionController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'find');
                Route::put('/update/{id}', 'update');
                Route::delete('/destroy/{id}', 'destroy');
            });

        Route::prefix('/payment-webhook-logs')
            ->controller(PaymentWebhookLogController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'find');
                Route::put('/update/{id}', 'update');
                Route::delete('/destroy/{id}', 'destroy');
            });

        Route::prefix('/payments')
            ->controller(PaymentController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'find');
                Route::put('/update/{id}', 'update');
                Route::delete('/destroy/{id}', 'destroy');
            });

        Route::prefix('/plan-features')
            ->controller(PlanFeatureController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'find');
                Route::put('/update/{id}', 'update');
                Route::delete('/destroy/{id}', 'destroy');
            });

        Route::prefix('/plan-prices')
            ->controller(PlanPriceController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'find');
                Route::put('/update/{id}', 'update');
                Route::delete('/destroy/{id}', 'destroy');
            });

        Route::prefix('/rate-limits')
            ->controller(RateLimitController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'find');
                Route::put('/update/{id}', 'update');
                Route::delete('/destroy/{id}', 'destroy');
            });

        Route::prefix('/subscription-events')
            ->controller(SubscriptionEventController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'find');
                Route::put('/update/{id}', 'update');
                Route::delete('/destroy/{id}', 'destroy');
            });

        Route::prefix('/subscription-items')
            ->controller(SubscriptionItemController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'find');
                Route::put('/update/{id}', 'update');
                Route::delete('/destroy/{id}', 'destroy');
            });

        Route::prefix('/subscription-orders')
            ->controller(SubscriptionOrderController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'find');
                Route::put('/update/{id}', 'update');
                Route::delete('/destroy/{id}', 'destroy');
            });

        Route::prefix('/subscription-order-items')
            ->controller(SubscriptionOrderItemController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'find');
                Route::put('/update/{id}', 'update');
                Route::delete('/destroy/{id}', 'destroy');
            });

        Route::prefix('/usage-records')
            ->controller(UsageRecordController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'find');
                Route::put('/update/{id}', 'update');
                Route::delete('/destroy/{id}', 'destroy');
            });

    });

// ================================ website routes =============================
Route::prefix('/v1')
    ->group(function () {
        Route::get('/subscription-plans', [PlanController::class, 'index']);
        Route::get('/plans/{id}', [PlanController::class, 'find']);
        Route::get('/subscription-plans/{slug}', [PlanController::class, 'findBySlug']);
        Route::get('/features', [FeatureController::class, 'index']);

        // checkut routes
        Route::post('/checkout/process', [SubscriptionController::class, 'process'])->name('checkout.process');

        Route::post('/checkout/initialize', [CheckoutController::class, 'initialize']);
        Route::post('/checkout/send-otp', [CheckoutController::class, 'sendOtp']);
        Route::post('/checkout/verify-otp', [CheckoutController::class, 'verifyOtpAndCheckout']);
        // Protected checkout routes
        Route::middleware('auth:sanctum')->post('/checkout/process-authenticated', [CheckoutController::class, 'processAuthenticated']);
    });

// Protected API routes
Route::middleware('auth:sanctum')
    ->prefix('/v1')
    ->group(function () {
        // Subscriptions
        Route::get('/my-subscriptions', [SubscriptionController::class, 'getUserSubscriptions']);
        Route::get('/subscriptions/{id}', [SubscriptionController::class, 'show']);
        Route::post('/subscriptions/{id}/cancel', [SubscriptionController::class, 'cancel']);
        Route::post('/subscriptions/{id}/refund', [SubscriptionController::class, 'refund']);
        Route::post('/subscriptions/{id}/renew', [SubscriptionController::class, 'renew']);
        Route::get('/subscriptions/{id}/invoices', [SubscriptionController::class, 'invoices']);
        Route::get('/subscriptions/{id}/usage', [SubscriptionController::class, 'usage']);
        Route::get('/subscriptions/{id}/events', [SubscriptionController::class, 'events']);
        Route::post('/subscriptions/{id}/payment-method', [SubscriptionController::class, 'updatePaymentMethod']);

        Route::get('/usage', [UsageController::class, 'index']);
        Route::get('/usage/{subscriptionId}', [UsageController::class, 'show']);
        Route::get('/usage/statistics/overview', [UsageController::class, 'statistics']);
        Route::get('/current-billing', [UsageController::class, 'currentBilling']);

        // Invoices
        Route::get('/invoices', [InvoiceController::class, 'index']);
        Route::get('/invoices/{id}', [InvoiceController::class, 'show']);
        Route::get('/invoices/{id}/download', [InvoiceController::class, 'download']);

        // Payment Methods
        Route::get('/payment-methods', [PaymentController::class, 'getMethods']);
        Route::post('/payment-methods', [PaymentController::class, 'addMethod']);
        Route::delete('/payment-methods/{id}', [PaymentController::class, 'removeMethod']);
        Route::put('/payment-methods/{id}/default', [PaymentController::class, 'setDefault']);
        // settings
        Route::get('user/settings', [SubscriptionController::class, 'settings']);
    });

Route::prefix('/v1')
    ->group(function () {
        Route::get('/payment-gateways', [PaymentGatewayController::class, 'index']);
    });

// stripe
Route::middleware('auth:sanctum')
    ->prefix('/v1')
    ->group(function () {
        Route::post('/checkout/confirm-stripe', [CheckoutController::class, 'confirmStripePayment']);
    });

// ==================== PAYMENT CALLBACK ROUTES (Public, No Auth) ====================
Route::prefix('payment')->name('payment.')->group(function () {
    // ডায়নামিক কলব্যাক হ্যান্ডলার - সব গেটওয়ের জন্য একটি রাউট
    Route::match(['get', 'post'], '/callback/{gateway}/{status?}', [CheckoutController::class, 'handleCallback'])
        ->name('callback')
        ->where('gateway', 'stripe|paypal|bkash|nagad|rocket|surjopay|sslcommerz|cash|bank-transfer')
        ->where('status', 'success|fail|cancel|ipn|pending');

    // ডায়নামিক স্পেসিফিক রাউট (যেমন: /payment/stripe/success)
    Route::match(['get', 'post'], '/{gateway}/{status?}', [CheckoutController::class, 'handleCallback'])
        ->name('gateway.callback')
        ->where('gateway', 'stripe|paypal|bkash|nagad|rocket|surjopay|sslcommerz|cash|bank-transfer')
        ->where('status', 'success|fail|cancel|ipn|pending');

    // ডায়নামিক আইপিএন রাউট (পোস্ট মেথড)
    Route::post('/{gateway}/ipn', [CheckoutController::class, 'handleCallback'])
        ->name('gateway.ipn')
        ->where('gateway', 'stripe|paypal|bkash|nagad|rocket|surjopay|sslcommerz')
        ->defaults('status', 'ipn');

    // বিশেষ কেস: PayPal subscription success
    Route::get('/{gateway}/subscription/success', [CheckoutController::class, 'handleCallback'])
        ->name('gateway.subscription.success')
        ->where('gateway', 'paypal')
        ->defaults('status', 'success');
});

// ==================== WEBHOOK ROUTES (Public) ====================
Route::prefix('webhooks')->name('webhooks.')->group(function () {
    // ডায়নামিক ওয়েবহুক হ্যান্ডলার
    Route::post('/{gateway}', [WebhookController::class, 'handleWebhook'])
        ->name('handle')
        ->where('gateway', 'stripe|paypal|bkash|nagad|rocket|surjopay|sslcommerz');
});

// ==================== HEALTH CHECK ====================
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});

// // Basic subscription check (any active subscription or free plan)
// Route::middleware(['auth', 'subscription'])->group(function () {
//     Route::get('/crud-generator', [CrudGeneratorController::class, 'create']);
//     Route::post('/crud-generator/generate', [CrudGeneratorController::class, 'generate']);
// });

// // Specific plan check (e.g., 'starter', 'professional', 'enterprise')
// Route::middleware(['auth', 'subscription:professional'])->group(function () {
//     Route::get('/advanced-features', [AdvancedFeatureController::class, 'index']);
// });

// // Check specific feature (e.g., 'crud_generation' feature)
// Route::middleware(['auth', 'subscription:any,crud_generation'])->group(function () {
//     Route::get('/generate-crud', [CrudGeneratorController::class, 'create']);
// });

// // API routes with subscription check
// Route::middleware(['auth:sanctum', 'subscription'])->prefix('v1')->group(function () {
//     Route::post('/crud/generate', [CrudGeneratorController::class, 'generate']);
// });

// Web routes with middleware
Route::middleware(['auth:sanctum'])
    ->prefix('v1')
    ->group(function () {

        // Check subscription + usage for CRUD generation
        Route::middleware(['subscription', 'usage:crud_generation,1'])->group(function () {
            Route::get('/crud-generator', [\App\Http\Controllers\CrudGeneratorController::class, 'create'])->name('crud.generator.create');
            Route::post('/crud-generator/generate', [\App\Http\Controllers\CrudGeneratorController::class, 'generate'])->name('crud.generator.generate');
        });

        // Usage statistics
        Route::get('/usage-stats', [CrudGeneratorController::class, 'usageStats'])->name('usage.stats');
        Route::get('/usage-forecast', [\App\Http\Controllers\CrudGeneratorController::class, 'usageForecast'])->name('usage.forecast');
    });

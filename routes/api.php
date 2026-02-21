<?php

use App\Http\Controllers\Api\V1\DiscountController;
use App\Http\Controllers\Api\V1\FeatureController;
use App\Http\Controllers\Api\V1\HkProdUomController;
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
                Route::get('/', 'index');
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


    //================================ website routes =============================
Route::prefix('/v1')
    ->group(function () {
        Route::get('/subscription-plans', [PlanController::class, 'index']);
        Route::get('/plans/{id}', [PlanController::class, 'find']);
        Route::get('/subscription-plans/{slug}', [PlanController::class, 'findBySlug']);
        Route::get('/features', [FeatureController::class, 'index']);
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

// Public webhook routes
Route::post('/webhooks/stripe', [WebhookController::class, 'handleStripe']);
Route::post('/webhooks/paypal', [WebhookController::class, 'handlePayPal']);
Route::post('/webhooks/bkash', [WebhookController::class, 'handleBkash']);
Route::post('/webhooks/surjopay', [WebhookController::class, 'handleSurjoPay']);

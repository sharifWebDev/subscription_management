<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->bind(
            \App\Repositories\Contracts\InvoiceRepositoryInterface::class,
            \App\Repositories\InvoiceRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\RefundRepositoryInterface::class,
            \App\Repositories\RefundRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\UsageRecordRepositoryInterface::class,
            \App\Repositories\UsageRecordRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\SubscriptionRepositoryInterface::class,
            \App\Repositories\SubscriptionRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\SubscriptionOrderRepositoryInterface::class,
            \App\Repositories\SubscriptionOrderRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\SubscriptionOrderItemRepositoryInterface::class,
            \App\Repositories\SubscriptionOrderItemRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\SubscriptionItemRepositoryInterface::class,
            \App\Repositories\SubscriptionItemRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\SubscriptionEventRepositoryInterface::class,
            \App\Repositories\SubscriptionEventRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\RefundRepositoryInterface::class,
            \App\Repositories\RefundRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\RateLimitRepositoryInterface::class,
            \App\Repositories\RateLimitRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PlanRepositoryInterface::class,
            \App\Repositories\PlanRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PlanPriceRepositoryInterface::class,
            \App\Repositories\PlanPriceRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PlanFeatureRepositoryInterface::class,
            \App\Repositories\PlanFeatureRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PaymentRepositoryInterface::class,
            \App\Repositories\PaymentRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PaymentWebhookLogRepositoryInterface::class,
            \App\Repositories\PaymentWebhookLogRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PaymentTransactionRepositoryInterface::class,
            \App\Repositories\PaymentTransactionRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PaymentMethodRepositoryInterface::class,
            \App\Repositories\PaymentMethodRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PaymentMasterRepositoryInterface::class,
            \App\Repositories\PaymentMasterRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PaymentGatewayRepositoryInterface::class,
            \App\Repositories\PaymentGatewayRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PaymentChildRepositoryInterface::class,
            \App\Repositories\PaymentChildRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PaymentAllocationRepositoryInterface::class,
            \App\Repositories\PaymentAllocationRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\MeteredUsageAggregateRepositoryInterface::class,
            \App\Repositories\MeteredUsageAggregateRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\InvoiceRepositoryInterface::class,
            \App\Repositories\InvoiceRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\FeatureRepositoryInterface::class,
            \App\Repositories\FeatureRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\DiscountRepositoryInterface::class,
            \App\Repositories\DiscountRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\HkProdUomRepositoryInterface::class,
            \App\Repositories\HkProdUomRepository::class
        );

        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

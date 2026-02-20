<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('plan_id')->constrained('plans', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('plan_price_id')->constrained('plan_prices', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('parent_subscription_id', 255)->nullable();
            $table->string('status', 255)->default('trialing');
            $table->string('billing_cycle_anchor', 255)->default('creation');
            $table->string('quantity', 11)->default(1);
            $table->decimal('unit_price', 20, 8);
            $table->decimal('amount', 20, 8);
            $table->string('currency', 255)->default('USD');
            $table->dateTime('trial_starts_at')->nullable();
            $table->dateTime('trial_ends_at')->nullable();
            $table->boolean('trial_converted')->default(false);
            $table->dateTime('current_period_starts_at')->nullable();
            $table->dateTime('current_period_ends_at')->nullable();
            $table->dateTime('billing_cycle_anchor_date')->nullable();
            $table->dateTime('canceled_at')->nullable();
            $table->string('cancellation_reason', 255)->nullable();
            $table->boolean('prorate')->default(true);
            $table->decimal('proration_amount', 20, 8)->nullable();
            $table->dateTime('proration_date')->nullable();
            $table->string('gateway', 255)->default('stripe');
            $table->string('gateway_subscription_id', 255)->nullable();
            $table->string('gateway_customer_id', 255)->nullable();
            $table->json('gateway_metadata')->nullable();
            $table->json('metadata')->nullable();
            $table->json('history')->nullable();
            $table->boolean('is_active')->default(false);
            // $table->foreign('user_id')->references('id')->on('users');
            // $table->foreign('plan_id')->references('id')->on('plans');
            // $table->foreign('plan_price_id')->references('id')->on('plan_prices');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};

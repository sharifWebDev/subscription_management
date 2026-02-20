<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->char('user_id', 36);
            $table->char('plan_id', 36);
            $table->char('plan_price_id', 36);
            $table->char('parent_subscription_id', 36)->nullable();
            $table->enum('status', ['active', 'trialing', 'past_due', 'canceled', 'unpaid', 'incomplete', 'incomplete_expired', 'paused', 'suspended'])->default('trialing');
            $table->enum('billing_cycle_anchor', ['creation', 'billing_cycle'])->default('creation');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 20, 8);
            $table->decimal('amount', 20, 8);
            $table->char('currency', 3)->default('USD');
            $table->timestamp('trial_starts_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->boolean('trial_converted')->default(false);
            $table->timestamp('current_period_starts_at')->nullable();
            $table->timestamp('current_period_ends_at')->nullable();
            $table->timestamp('billing_cycle_anchor_date')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->enum('cancellation_reason', ['customer', 'payment_failed', 'fraud', 'business', 'upgrade', 'downgrade', 'other'])->nullable();
            $table->boolean('prorate')->default(true);
            $table->decimal('proration_amount', 20, 8)->nullable();
            $table->timestamp('proration_date')->nullable();
            $table->string('gateway', 50)->default('stripe');
            $table->string('gateway_subscription_id', 255)->nullable();
            $table->string('gateway_customer_id', 255)->nullable();
            $table->json('gateway_metadata')->nullable();
            $table->json('metadata')->nullable();
            $table->json('history')->nullable();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->timestamp('deleted_at')->nullable();
            $table->boolean('is_active')->default(false);

            $table->index(['user_id', 'status', 'current_period_ends_at'], 'user_active_subscription_idx');
            $table->index(['status', 'current_period_ends_at'], 'expiring_subscriptions_idx');
            $table->index(['gateway', 'gateway_subscription_id'], 'gateway_subscription_lookup');
            $table->index(['parent_subscription_id', 'status'], 'child_subscriptions_idx');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('plan_price_id')->references('id')->on('plan_prices')->onDelete('restrict')->onUpdate('cascade');
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

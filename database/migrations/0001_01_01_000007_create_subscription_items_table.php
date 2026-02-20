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
        Schema::create('subscription_items', function (Blueprint $table) {
            $table->id();
            $table->char('subscription_id', 36);
            $table->char('plan_price_id', 36);
            $table->char('feature_id', 36);
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 20, 8);
            $table->decimal('amount', 20, 8);
            $table->json('metadata')->nullable();
            $table->json('tiers')->nullable();
            $table->timestamp('effective_from')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('effective_to')->nullable();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->timestamp('deleted_at')->nullable();

            $table->unique(['subscription_id', 'plan_price_id', 'feature_id'], 'subscription_item_unique');
            $table->index(['subscription_id', 'amount'], 'subscription_pricing_idx');

            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('plan_price_id')->references('id')->on('plan_prices')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('feature_id')->references('id')->on('features')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_items');
    }
};

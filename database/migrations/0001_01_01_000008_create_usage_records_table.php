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
        Schema::create('usage_records', function (Blueprint $table) {
            $table->id();
            $table->char('subscription_id', 36);
            $table->char('subscription_item_id', 36);
            $table->char('feature_id', 36);
            $table->decimal('quantity', 20, 8);
            $table->decimal('tier_quantity', 20, 8)->nullable();
            $table->decimal('amount', 20, 8)->nullable();
            $table->string('unit', 50);
            $table->enum('status', ['pending', 'billed', 'void', 'disputed'])->default('pending')-
            $table->timestamp('recorded_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->date('billing_date');
            $table->json('metadata')->nullable();
            $table->json('dimensions')->nullable();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->timestamp('deleted_at')->nullable();

            $table->index(['subscription_id', 'billing_date', 'feature_id'], 'subscription_usage_idx');
            $table->index(['billing_date', 'feature_id', 'status'], 'billing_aggregation_idx');
            $table->index(['recorded_at'], 'record_timestamp_idx');

            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('subscription_item_id')->references('id')->on('subscription_items')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('feature_id')->references('id')->on('features')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usage_records');
    }
};

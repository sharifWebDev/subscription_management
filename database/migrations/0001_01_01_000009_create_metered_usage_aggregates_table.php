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
        Schema::create('metered_usage_aggregates', function (Blueprint $table) {
            $table->id();
            $table->char('subscription_id', 36);
            $table->char('feature_id', 36);
            $table->date('aggregate_date');
            $table->enum('aggregate_period', ['daily', 'weekly', 'monthly', 'yearly']);
            $table->decimal('total_quantity', 20, 8);
            $table->decimal('tier1_quantity', 20, 8)->default(0);
            $table->decimal('tier2_quantity', 20, 8)->default(0);
            $table->decimal('tier3_quantity', 20, 8)->default(0);
            $table->decimal('total_amount', 20, 8)->default(0);
            $table->integer('record_count');
            $table->timestamp('last_calculated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->timestamp('deleted_at')->nullable();

            $table->unique(['subscription_id', 'feature_id', 'aggregate_date', 'aggregate_period'], 'usage_aggregate_unique');
            $table->index(['subscription_id', 'aggregate_date', 'feature_id'], 'rollup_usage_idx');
            $table->index(['aggregate_date', 'feature_id', 'total_quantity'], 'global_usage_trends_idx');

            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('feature_id')->references('id')->on('features')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metered_usage_aggregates');
    }
};

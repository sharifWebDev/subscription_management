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
        Schema::create('plan_prices', function (Blueprint $table) {
            $table->id();
            $table->char('plan_id', 36);
            $table->char('currency', 3)->default('USD');
            $table->decimal('amount', 20, 8);
            $table->enum('interval', ['month', 'year', 'quarter', 'week', 'day']);
            $table->integer('interval_count')->default(1);
            $table->enum('usage_type', ['licensed', 'metered', 'tiered'])->default('licensed');
            $table->json('tiers')->nullable();
            $table->json('transformations')->nullable();
            $table->string('stripe_price_id', 255)->nullable();
            $table->timestamp('active_from')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('active_to')->nullable();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->timestamp('deleted_at')->nullable();

            $table->unique('stripe_price_id');
            $table->unique(['plan_id', 'currency', 'interval', 'interval_count'], 'plan_price_unique');
            $table->index(['plan_id', 'currency', 'interval'], 'active_price_idx');

            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_prices');
    }
};

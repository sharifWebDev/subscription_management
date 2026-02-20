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
        Schema::create('metered_usage_aggregates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('feature_id')->constrained('features', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('aggregate_date');
            $table->string('aggregate_period', 255);
            $table->decimal('total_quantity', 20, 8);
            $table->decimal('tier1_quantity', 20, 8)->default(0.00000000);
            $table->decimal('tier2_quantity', 20, 8)->default(0.00000000);
            $table->decimal('tier3_quantity', 20, 8)->default(0.00000000);
            $table->decimal('total_amount', 20, 8)->default(0.00000000);
            $table->string('record_count', 11);
            $table->dateTime('last_calculated_at')->useCurrent();
            // $table->foreign('subscription_id')->references('id')->on('subscriptions');
            // $table->foreign('feature_id')->references('id')->on('features');
            $table->timestamps();
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

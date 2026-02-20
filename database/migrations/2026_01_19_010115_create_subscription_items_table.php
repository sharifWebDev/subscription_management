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
        Schema::create('subscription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('plan_price_id')->constrained('plan_prices', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('feature_id')->constrained('features', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('quantity', 11)->default(1);
            $table->decimal('unit_price', 20, 8);
            $table->decimal('amount', 20, 8);
            $table->json('metadata')->nullable();
            $table->json('tiers')->nullable();
            $table->dateTime('effective_from')->useCurrent();
            $table->dateTime('effective_to')->nullable();
            // $table->foreign('subscription_id')->references('id')->on('subscriptions');
            // $table->foreign('plan_price_id')->references('id')->on('plan_prices');
            // $table->foreign('feature_id')->references('id')->on('features');
            $table->timestamps();
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

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
            $table->foreignId('plan_id')->constrained('plans', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('currency', 255)->default('USD');
            $table->decimal('amount', 20, 8);
            $table->string('interval', 255);
            $table->string('interval_count', 11)->default(1);
            $table->string('usage_type', 255)->default('licensed');
            $table->json('tiers')->nullable();
            $table->json('transformations')->nullable();
            $table->string('stripe_price_id', 255)->nullable();
            $table->dateTime('active_from')->useCurrent();
            $table->dateTime('active_to')->nullable();
            // $table->foreign('plan_id')->references('id')->on('plans');
            $table->timestamps();
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

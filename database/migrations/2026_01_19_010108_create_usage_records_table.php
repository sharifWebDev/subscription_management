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
        Schema::create('usage_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('subscription_item_id')->constrained('subscription_items', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('feature_id')->constrained('features', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->decimal('quantity', 20, 8);
            $table->decimal('tier_quantity', 20, 8)->nullable();
            $table->decimal('amount', 20, 8)->nullable();
            $table->string('unit', 255);
            $table->string('status', 255)->default('pending');
            $table->dateTime('recorded_at')->useCurrent();
            $table->date('billing_date');
            $table->json('metadata')->nullable();
            $table->json('dimensions')->nullable();
            // $table->foreign('subscription_id')->references('id')->on('subscriptions');
            // $table->foreign('subscription_item_id')->references('id')->on('subscription_items');
            // $table->foreign('feature_id')->references('id')->on('features');
            $table->timestamps();
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

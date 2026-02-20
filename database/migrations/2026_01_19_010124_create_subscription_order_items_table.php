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
        Schema::create('subscription_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_order_id')->constrained('subscription_orders', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('plan_id')->constrained('plans', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('plan_name', 255);
            $table->string('billing_cycle', 255);
            $table->string('quantity', 11)->default(1);
            $table->foreignId('recipient_user_id')->nullable()->constrained('users', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->json('recipient_info')->nullable();
            $table->decimal('unit_price', 20, 8);
            $table->decimal('amount', 20, 8);
            $table->decimal('tax_amount', 20, 8)->default(0.00000000);
            $table->decimal('discount_amount', 20, 8)->default(0.00000000);
            $table->decimal('total_amount', 20, 8);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('subscription_status', 255)->default('pending');
            $table->text('processing_error')->nullable();
            $table->dateTime('processed_at')->nullable();
            // $table->foreign('subscription_order_id')->references('id')->on('subscription_orders');
            // $table->foreign('plan_id')->references('id')->on('plans');
            // $table->foreign('user_id')->references('id')->on('users');
            // $table->foreign('recipient_user_id')->references('id')->on('users');
            // $table->foreign('subscription_id')->references('id')->on('subscriptions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_order_items');
    }
};

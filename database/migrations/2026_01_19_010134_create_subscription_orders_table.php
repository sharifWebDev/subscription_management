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
        Schema::create('subscription_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('payment_master_id')->nullable()->constrained('payment_masters', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('order_number', 255);
            $table->string('status', 255)->default('draft');
            $table->string('type', 255)->default('new');
            $table->decimal('subtotal', 20, 8)->default(0.00000000);
            $table->decimal('tax_amount', 20, 8)->default(0.00000000);
            $table->decimal('discount_amount', 20, 8)->default(0.00000000);
            $table->decimal('total_amount', 20, 8)->default(0.00000000);
            $table->string('currency', 255)->default('USD');
            $table->json('customer_info')->nullable();
            $table->json('billing_address')->nullable();
            $table->dateTime('ordered_at')->nullable();
            $table->dateTime('processed_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->string('coupon_code', 255)->nullable();
            $table->json('applied_discounts')->nullable();
            $table->json('metadata')->nullable();
            $table->text('notes')->nullable();
            $table->text('failure_reason')->nullable();
            // $table->foreign('user_id')->references('id')->on('users');
            // $table->foreign('payment_master_id')->references('id')->on('payment_masters');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_orders');
    }
};

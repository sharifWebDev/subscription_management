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
            $table->char('user_id', 36);
            $table->char('payment_master_id', 36)->nullable();
            $table->string('order_number', 50)->unique();
            $table->enum('status', ['draft', 'pending', 'processing', 'completed', 'cancelled', 'failed'])->default('draft');
            $table->enum('type', ['new', 'renewal', 'upgrade', 'downgrade', 'bulk'])->default('new');
            $table->decimal('subtotal', 20, 8)->default(0);
            $table->decimal('tax_amount', 20, 8)->default(0);
            $table->decimal('discount_amount', 20, 8)->default(0);
            $table->decimal('total_amount', 20, 8)->default(0);
            $table->char('currency', 3)->default('USD');
            $table->json('customer_info')->nullable();
            $table->json('billing_address')->nullable();
            $table->timestamp('ordered_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('coupon_code', 100)->nullable();
            $table->json('applied_discounts')->nullable();
            $table->json('metadata')->nullable();
            $table->text('notes')->nullable();
            $table->text('failure_reason')->nullable();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status', 'ordered_at'], 'user_subscription_orders_idx');
            $table->index(['order_number', 'type'], 'order_lookup_idx');
            $table->index(['status', 'ordered_at'], 'order_processing_idx');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('payment_master_id')->references('id')->on('payment_masters')->onDelete('set null')->onUpdate('cascade');
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

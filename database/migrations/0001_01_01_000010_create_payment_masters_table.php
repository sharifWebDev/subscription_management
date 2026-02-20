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
        Schema::create('payment_masters', function (Blueprint $table) {
            $table->id();
            $table->char('user_id', 36);
            $table->string('payment_number', 50);
            $table->enum('type', ['subscription', 'order', 'wallet_topup', 'refund', 'adjustment', 'bulk']);
            $table->enum('status', ['draft', 'pending', 'processing', 'partially_paid', 'paid', 'failed', 'refunded', 'disputed', 'cancelled', 'expired'])->default('draft');
            $table->decimal('total_amount', 20, 8)->default(0);
            $table->decimal('subtotal', 20, 8)->default(0);
            $table->decimal('tax_amount', 20, 8)->default(0);
            $table->decimal('discount_amount', 20, 8)->default(0);
            $table->decimal('fee_amount', 20, 8)->default(0);
            $table->decimal('net_amount', 20, 8)->default(0);
            $table->decimal('paid_amount', 20, 8)->default(0);
            $table->decimal('due_amount', 20, 8)->generatedAs('total_amount - paid_amount')->stored();
            $table->char('currency', 3)->default('USD');
            $table->decimal('exchange_rate', 12, 6)->default(1);
            $table->char('base_currency', 3)->default('USD');
            $table->decimal('base_amount', 20, 8)->generatedAs('total_amount * exchange_rate')->stored();
            $table->enum('payment_method', ['cash', 'bank_transfer', 'stripe', 'paypal', 'sslcommerz', 'card', 'bkash', 'nagad', 'rocket', 'google_pay', 'apple_pay', 'crypto', 'wallet', 'cheque', 'installment', 'custom'])->nullable();
            $table->json('payment_method_details')->nullable();
            $table->string('payment_gateway', 50)->nullable();
            $table->boolean('is_installment')->default(false);
            $table->integer('installment_count')->nullable();
            $table->string('installment_frequency', 20)->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('customer_reference', 100)->nullable();
            $table->string('bank_reference', 100)->nullable();
            $table->string('gateway_reference', 100)->nullable();
            $table->json('metadata')->nullable();
            $table->json('custom_fields')->nullable();
            $table->text('notes')->nullable();
            $table->text('failure_reason')->nullable();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->timestamp('deleted_at')->nullable();

            $table->unique('payment_number');
            $table->index(['user_id', 'status', 'payment_date'], 'user_payment_status_idx');
            $table->index(['payment_number', 'type'], 'payment_lookup_idx');
            $table->index(['payment_method', 'status', 'payment_date'], 'payment_method_analytics_idx');
            $table->index(['due_date', 'status'], 'pending_payments_idx');
            $table->fullText(['payment_number', 'customer_reference', 'notes'], 'payment_search_ft_idx');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_masters');
    }
};

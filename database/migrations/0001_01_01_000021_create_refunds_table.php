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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->char('payment_master_id', 36);
            $table->char('payment_transaction_id', 36);
            $table->char('user_id', 36);
            $table->string('refund_number', 50)->unique();
            $table->enum('type', ['full', 'partial', 'chargeback', 'dispute']);
            $table->enum('status', ['requested', 'approved', 'processing', 'completed', 'failed', 'rejected'])->default('requested');
            $table->enum('initiated_by', ['customer', 'merchant', 'gateway', 'system'])->default('customer');
            $table->decimal('amount', 20, 8);
            $table->decimal('fee', 20, 8)->default(0);
            $table->decimal('net_amount', 20, 8)->storedAs('amount - fee');
            $table->char('currency', 3)->default('USD');
            $table->decimal('exchange_rate', 12, 6)->default(1);
            $table->enum('reason', ['duplicate', 'fraudulent', 'requested_by_customer', 'credit_not_processed', 'goods_not_received', 'goods_defective', 'subscription_cancelled', 'other'])->default('other');
            $table->text('reason_details')->nullable();
            $table->text('customer_comments')->nullable();
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('approved_at')->nullable();
            $table->char('approved_by', 36)->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->string('gateway_refund_id', 255)->nullable();
            $table->json('gateway_response')->nullable();
            $table->json('metadata')->nullable();
            $table->json('documents')->nullable();
            $table->char('processed_by', 36)->nullable();
            $table->text('rejection_reason')->nullable();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status', 'requested_at'], 'user_refunds_idx');
            $table->index(['payment_master_id', 'status'], 'payment_refunds_idx');

            $table->foreign('payment_master_id')->references('id')->on('payment_masters')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('payment_transaction_id')->references('id')->on('payment_transactions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};

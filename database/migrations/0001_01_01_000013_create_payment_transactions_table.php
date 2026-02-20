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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->char('payment_master_id', 36);
            $table->char('payment_child_id', 36)->nullable();
            $table->string('transaction_id', 100);
            $table->string('reference_id', 255)->nullable();
            $table->enum('type', ['payment', 'refund', 'chargeback', 'dispute', 'adjustment', 'reversal', 'settlement'])->default('payment');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'stripe', 'paypal', 'sslcommerz', 'card', 'bkash', 'nagad', 'rocket', 'google_pay', 'apple_pay', 'crypto', 'wallet', 'cheque', 'installment']);
            $table->string('payment_gateway', 50);
            $table->json('gateway_response')->nullable();
            $table->json('payment_method_details')->nullable();
            $table->decimal('amount', 20, 8);
            $table->decimal('fee', 20, 8)->default(0);
            $table->decimal('tax', 20, 8)->default(0);
            $table->decimal('net_amount', 20, 8)->generatedAs('amount - fee')->stored();
            $table->char('currency', 3)->default('USD');
            $table->decimal('exchange_rate', 12, 6)->default(1);
            $table->enum('status', ['initiated', 'authorized', 'captured', 'pending', 'completed', 'failed', 'refunded', 'charged_back', 'disputed', 'cancelled', 'expired'])->default('initiated');
            $table->char('card_last4', 4)->nullable();
            $table->string('card_brand', 20)->nullable();
            $table->char('card_country', 2)->nullable();
            $table->integer('card_exp_month')->nullable();
            $table->integer('card_exp_year')->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->char('bank_account_last4', 4)->nullable();
            $table->string('bank_routing_number', 50)->nullable();
            $table->string('wallet_type', 50)->nullable();
            $table->string('wallet_number', 20)->nullable();
            $table->string('wallet_transaction_id', 100)->nullable();
            $table->integer('installment_number')->nullable();
            $table->integer('total_installments')->nullable();
            $table->timestamp('initiated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('authorized_at')->nullable();
            $table->timestamp('captured_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->json('fraud_indicators')->nullable();
            $table->decimal('risk_score', 5, 2)->nullable();
            $table->boolean('requires_review')->default(false);
            $table->json('metadata')->nullable();
            $table->json('custom_fields')->nullable();
            $table->text('notes')->nullable();
            $table->text('failure_reason')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('location_data')->nullable();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->timestamp('deleted_at')->nullable();

            $table->unique('transaction_id');
            $table->index(['payment_master_id', 'status'], 'master_transaction_status_idx');
            $table->index(['transaction_id', 'reference_id'], 'transaction_lookup_idx');
            $table->index(['payment_method', 'status', 'completed_at'], 'payment_method_stats_idx');
            $table->index(['payment_gateway', 'status', 'completed_at'], 'gateway_stats_idx');
            $table->index(['card_brand', 'status'], 'card_analytics_idx');
            $table->index(['completed_at', 'amount'], 'revenue_analytics_idx');

            $table->foreign('payment_master_id')->references('id')->on('payment_masters')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('payment_child_id')->references('id')->on('payment_children')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};

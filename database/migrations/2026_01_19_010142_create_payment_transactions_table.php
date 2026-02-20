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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_master_id')->constrained('payment_masters', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('payment_child_id')->nullable()->constrained('payment_children', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('transaction_id', 255);
            $table->string('reference_id', 255)->nullable();
            $table->string('type', 255)->default('payment');
            $table->string('payment_method', 255);
            $table->string('payment_gateway', 255);
            $table->json('gateway_response')->nullable();
            $table->json('payment_method_details')->nullable();
            $table->decimal('amount', 20, 8);
            $table->decimal('fee', 20, 8)->default(0.00000000);
            $table->decimal('tax', 20, 8)->default(0.00000000);
            $table->decimal('net_amount', 20, 8)->nullable();
            $table->string('currency', 255)->default('USD');
            $table->decimal('exchange_rate', 12, 6)->default(1.000000);
            $table->string('status', 255)->default('initiated');
            $table->string('card_last4', 255)->nullable();
            $table->string('card_brand', 255)->nullable();
            $table->string('card_country', 255)->nullable();
            $table->string('card_exp_month', 11)->nullable();
            $table->string('card_exp_year', 11)->nullable();
            $table->string('bank_name', 255)->nullable();
            $table->string('bank_account_last4', 255)->nullable();
            $table->string('bank_routing_number', 255)->nullable();
            $table->string('wallet_type', 255)->nullable();
            $table->string('wallet_number', 255)->nullable();
            $table->string('wallet_transaction_id', 255)->nullable();
            $table->string('installment_number', 11)->nullable();
            $table->string('total_installments', 11)->nullable();
            $table->dateTime('initiated_at')->useCurrent();
            $table->dateTime('authorized_at')->nullable();
            $table->dateTime('captured_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('failed_at')->nullable();
            $table->dateTime('refunded_at')->nullable();
            $table->json('fraud_indicators')->nullable();
            $table->decimal('risk_score', 5, 2)->nullable();
            $table->boolean('requires_review')->default(false);
            $table->json('metadata')->nullable();
            $table->json('custom_fields')->nullable();
            $table->text('notes')->nullable();
            $table->text('failure_reason')->nullable();
            $table->string('ip_address', 255)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('location_data')->nullable();
            // $table->foreign('payment_master_id')->references('id')->on('payment_masters');
            // $table->foreign('payment_child_id')->references('id')->on('payment_children');
            $table->timestamps();
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

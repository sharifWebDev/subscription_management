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
            $table->foreignId('payment_master_id')->constrained('payment_masters', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('payment_transaction_id')->constrained('payment_transactions', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('refund_number', 255);
            $table->string('type', 255);
            $table->string('status', 255)->default('requested');
            $table->string('initiated_by', 255)->default('customer');
            $table->decimal('amount', 20, 8);
            $table->decimal('fee', 20, 8)->default(0.00000000);
            $table->decimal('net_amount', 20, 8)->nullable();
            $table->string('currency', 255)->default('USD');
            $table->decimal('exchange_rate', 12, 6)->default(1.000000);
            $table->string('reason', 255)->default('other');
            $table->text('reason_details')->nullable();
            $table->text('customer_comments')->nullable();
            $table->dateTime('requested_at')->useCurrent();
            $table->dateTime('approved_at')->nullable();
            $table->string('approved_by', 255)->nullable();
            $table->dateTime('processed_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('failed_at')->nullable();
            $table->string('gateway_refund_id', 255)->nullable();
            $table->json('gateway_response')->nullable();
            $table->json('metadata')->nullable();
            $table->json('documents')->nullable();
            $table->string('processed_by', 255)->nullable();
            $table->text('rejection_reason')->nullable();
            // $table->foreign('payment_master_id')->references('id')->on('payment_masters');
            // $table->foreign('payment_transaction_id')->references('id')->on('payment_transactions');
            // $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
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

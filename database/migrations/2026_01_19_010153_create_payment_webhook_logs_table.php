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
        Schema::create('payment_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_gateway_id')->nullable()->constrained('payment_gateways', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('gateway', 255);
            $table->string('event_type', 255);
            $table->string('webhook_id', 255)->nullable();
            $table->string('reference_id', 255)->nullable();
            $table->foreignId('payment_transaction_id')->nullable()->constrained('payment_transactions', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->json('payload')->nullable();
            $table->json('headers')->nullable();
            $table->string('response_code', 11)->nullable();
            $table->text('response_body')->nullable();
            $table->string('status', 255)->default('received');
            $table->text('processing_error')->nullable();
            $table->string('retry_count', 11)->default(0);
            $table->dateTime('next_retry_at')->nullable();
            $table->dateTime('received_at')->useCurrent();
            $table->dateTime('processed_at')->nullable();
            $table->string('ip_address', 255)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->string('verification_error', 255)->nullable();
            // $table->foreign('payment_gateway_id')->references('id')->on('payment_gateways');
            // $table->foreign('payment_transaction_id')->references('id')->on('payment_transactions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_webhook_logs');
    }
};

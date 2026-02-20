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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->char('invoice_id', 36)->nullable();
            $table->char('user_id', 36);
            $table->string('external_id', 255)->nullable()->unique();
            $table->enum('type', ['card', 'bank', 'wallet', 'crypto', 'cash', 'credit']);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'refunded', 'disputed']);
            $table->decimal('amount', 20, 8);
            $table->decimal('fee', 20, 8)->default(0);
            $table->decimal('net', 20, 8)->storedAs('amount - fee');
            $table->char('currency', 3)->default('USD');
            $table->string('gateway', 50);
            $table->json('gateway_response')->nullable();
            $table->json('payment_method')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->json('metadata')->nullable();
            $table->json('fraud_indicators')->nullable();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status', 'processed_at'], 'user_payments_idx');
            $table->index(['gateway', 'external_id'], 'gateway_payments_idx');
            $table->index(['type', 'status', 'processed_at'], 'payment_analytics_idx');

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

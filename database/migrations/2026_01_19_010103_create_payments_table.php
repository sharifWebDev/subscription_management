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
            $table->foreignId('invoice_id')->nullable()->constrained('invoices', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('external_id', 255)->nullable();
            $table->string('type', 255);
            $table->string('status', 255);
            $table->decimal('amount', 20, 8);
            $table->decimal('fee', 20, 8)->default(0.00000000);
            $table->decimal('net', 20, 8)->nullable();
            $table->string('currency', 255)->default('USD');
            $table->string('gateway', 255);
            $table->json('gateway_response')->nullable();
            $table->json('payment_method')->nullable();
            $table->dateTime('processed_at')->nullable();
            $table->dateTime('refunded_at')->nullable();
            $table->json('metadata')->nullable();
            $table->json('fraud_indicators')->nullable();
            // $table->foreign('invoice_id')->references('id')->on('invoices');
            // $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
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

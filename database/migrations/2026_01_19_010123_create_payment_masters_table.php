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
        Schema::create('payment_masters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('payment_number', 255);
            $table->string('type', 255);
            $table->string('status', 255)->default('draft');
            $table->decimal('total_amount', 20, 8)->default(0.00000000);
            $table->decimal('subtotal', 20, 8)->default(0.00000000);
            $table->decimal('tax_amount', 20, 8)->default(0.00000000);
            $table->decimal('discount_amount', 20, 8)->default(0.00000000);
            $table->decimal('fee_amount', 20, 8)->default(0.00000000);
            $table->decimal('net_amount', 20, 8)->default(0.00000000);
            $table->decimal('paid_amount', 20, 8)->default(0.00000000);
            $table->decimal('due_amount', 20, 8)->nullable();
            $table->string('currency', 255)->default('USD');
            $table->decimal('exchange_rate', 12, 6)->default(1.000000);
            $table->string('base_currency', 255)->default('USD');
            $table->decimal('base_amount', 20, 8)->nullable();
            $table->string('payment_method', 255)->nullable();
            $table->json('payment_method_details')->nullable();
            $table->string('payment_gateway', 255)->nullable();
            $table->boolean('is_installment')->default(false);
            $table->string('installment_count', 11)->nullable();
            $table->string('installment_frequency', 255)->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->string('customer_reference', 255)->nullable();
            $table->string('bank_reference', 255)->nullable();
            $table->string('gateway_reference', 255)->nullable();
            $table->json('metadata')->nullable();
            $table->json('custom_fields')->nullable();
            $table->text('notes')->nullable();
            $table->text('failure_reason')->nullable();
            // $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
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

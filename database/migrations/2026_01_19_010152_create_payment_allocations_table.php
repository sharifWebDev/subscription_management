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
        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_master_id')->constrained('payment_masters', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('payment_child_id')->constrained('payment_children', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('payment_transaction_id')->constrained('payment_transactions', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('allocatable_type', 255);
            $table->string('allocatable_id', 255);
            $table->decimal('amount', 20, 8);
            $table->decimal('base_amount', 20, 8)->nullable();
            $table->decimal('exchange_rate', 12, 6)->default(1.000000);
            $table->string('currency', 255)->default('USD');
            $table->string('allocation_reference', 255)->nullable();
            $table->string('allocation_type', 255)->default('payment');
            $table->boolean('is_reversed')->default(false);
            $table->dateTime('reversed_at')->nullable();
            $table->string('reversal_id', 255)->nullable();
            $table->json('metadata')->nullable();
            $table->text('notes')->nullable();
            // $table->foreign('payment_master_id')->references('id')->on('payment_masters');
            // $table->foreign('payment_child_id')->references('id')->on('payment_children');
            // $table->foreign('payment_transaction_id')->references('id')->on('payment_transactions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_allocations');
    }
};

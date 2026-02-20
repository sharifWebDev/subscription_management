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
        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->char('payment_master_id', 36);
            $table->char('payment_child_id', 36);
            $table->char('payment_transaction_id', 36);
            $table->string('allocatable_type', 100);
            $table->char('allocatable_id', 36);
            $table->decimal('amount', 20, 8);
            $table->decimal('base_amount', 20, 8)->generatedAs('amount * exchange_rate')->stored();
            $table->decimal('exchange_rate', 12, 6)->default(1);
            $table->char('currency', 3)->default('USD');
            $table->string('allocation_reference', 100)->nullable();
            $table->enum('allocation_type', ['payment', 'refund', 'credit', 'adjustment'])->default('payment');
            $table->boolean('is_reversed')->default(false);
            $table->timestamp('reversed_at')->nullable();
            $table->char('reversal_id', 36)->nullable();
            $table->json('metadata')->nullable();
            $table->text('notes')->nullable();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->timestamp('deleted_at')->nullable();

            $table->unique(['payment_transaction_id', 'allocatable_type', 'allocatable_id'], 'unique_allocation');
            $table->index(['payment_master_id', 'allocatable_type', 'allocatable_id'], 'master_allocation_idx');
            $table->index(['allocatable_type', 'allocatable_id', 'is_reversed'], 'item_allocations_idx');

            $table->foreign('payment_master_id')->references('id')->on('payment_masters')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('payment_child_id')->references('id')->on('payment_children')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('payment_transaction_id')->references('id')->on('payment_transactions')->onDelete('cascade')->onUpdate('cascade');
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

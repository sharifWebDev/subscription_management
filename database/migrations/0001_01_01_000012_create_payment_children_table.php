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
        Schema::create('payment_children', function (Blueprint $table) {
            $table->id();
            $table->char('payment_master_id', 36);
            $table->string('item_type', 100);
            $table->char('item_id', 36);
            $table->char('subscription_id', 36)->nullable();
            $table->char('plan_id', 36)->nullable();
            $table->char('invoice_id', 36)->nullable();
            $table->text('description');
            $table->string('item_code', 100)->nullable();
            $table->decimal('unit_price', 20, 8)->default(0);
            $table->integer('quantity')->default(1);
            $table->decimal('amount', 20, 8)->default(0);
            $table->decimal('tax_amount', 20, 8)->default(0);
            $table->decimal('discount_amount', 20, 8)->default(0);
            $table->decimal('total_amount', 20, 8)->default(0);
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->string('billing_cycle', 20)->nullable();
            $table->enum('status', ['pending', 'paid', 'refunded', 'cancelled', 'failed'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->decimal('allocated_amount', 20, 8)->default(0);
            $table->boolean('is_fully_allocated')->generatedAs('allocated_amount >= total_amount')->stored();
            $table->json('metadata')->nullable();
            $table->json('tax_breakdown')->nullable();
            $table->json('discount_breakdown')->nullable();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->timestamp('deleted_at')->nullable();

            $table->index(['payment_master_id', 'item_type', 'item_id'], 'payment_item_lookup_idx');
            $table->index(['subscription_id', 'status'], 'subscription_payments_idx');
            $table->index(['invoice_id', 'status'], 'invoice_payments_idx');
            $table->index(['item_type', 'item_id', 'status'], 'item_payment_status_idx');

            $table->foreign('payment_master_id')->references('id')->on('payment_masters')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_children');
    }
};

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
        Schema::create('payment_children', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_master_id')->constrained('payment_masters', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('item_type', 255);
            $table->string('item_id', 255);
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('plan_id')->nullable()->constrained('plans', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('description');
            $table->string('item_code', 255)->nullable();
            $table->decimal('unit_price', 20, 8)->default(0.00000000);
            $table->string('quantity', 11)->default(1);
            $table->decimal('amount', 20, 8)->default(0.00000000);
            $table->decimal('tax_amount', 20, 8)->default(0.00000000);
            $table->decimal('discount_amount', 20, 8)->default(0.00000000);
            $table->decimal('total_amount', 20, 8)->default(0.00000000);
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->string('billing_cycle', 255)->nullable();
            $table->string('status', 255)->default('pending');
            $table->dateTime('paid_at')->nullable();
            $table->decimal('allocated_amount', 20, 8)->default(0.00000000);
            $table->boolean('is_fully_allocated')->nullable();
            $table->json('metadata')->nullable();
            $table->json('tax_breakdown')->nullable();
            $table->json('discount_breakdown')->nullable();
            // $table->foreign('payment_master_id')->references('id')->on('payment_masters');
            // $table->foreign('subscription_id')->references('id')->on('subscriptions');
            // $table->foreign('plan_id')->references('id')->on('plans');
            // $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->timestamps();
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

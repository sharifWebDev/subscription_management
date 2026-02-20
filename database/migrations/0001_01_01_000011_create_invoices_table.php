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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->char('user_id', 36);
            $table->char('subscription_id', 36)->nullable();
            $table->string('number', 50);
            $table->string('external_id', 255)->nullable();
            $table->enum('type', ['subscription', 'one_time', 'credit', 'adjustment']);
            $table->enum('status', ['draft', 'open', 'paid', 'void', 'uncollectible', 'refunded']);
            $table->decimal('subtotal', 20, 8);
            $table->decimal('tax', 20, 8)->default(0);
            $table->decimal('total', 20, 8);
            $table->decimal('amount_due', 20, 8);
            $table->decimal('amount_paid', 20, 8)->default(0);
            $table->decimal('amount_remaining', 20, 8)->generatedAs('total - amount_paid')->stored();
            $table->char('currency', 3)->default('USD');
            $table->timestamp('issue_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->json('line_items')->nullable();
            $table->json('tax_rates')->nullable();
            $table->json('discounts')->nullable();
            $table->json('metadata')->nullable();
            $table->json('history')->nullable();
            $table->string('pdf_url', 500)->nullable();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->timestamp('deleted_at')->nullable();

            $table->unique('number');
            $table->unique('external_id');
            $table->index(['user_id', 'status', 'due_date'], 'user_invoice_status_idx');
            $table->index(['subscription_id', 'issue_date'], 'subscription_invoices_idx');
            $table->index(['external_id'], 'external_invoice_idx');
            $table->index(['type', 'status', 'issue_date'], 'invoice_analytics_idx');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

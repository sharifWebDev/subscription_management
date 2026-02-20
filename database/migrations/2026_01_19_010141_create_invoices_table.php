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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('number', 255);
            $table->string('external_id', 255)->nullable();
            $table->string('type', 255);
            $table->string('status', 255);
            $table->decimal('subtotal', 20, 8);
            $table->decimal('tax', 20, 8)->default(0.00000000);
            $table->decimal('total', 20, 8);
            $table->decimal('amount_due', 20, 8);
            $table->decimal('amount_paid', 20, 8)->default(0.00000000);
            $table->decimal('amount_remaining', 20, 8)->nullable();
            $table->string('currency', 255)->default('USD');
            $table->dateTime('issue_date')->useCurrent();
            $table->dateTime('due_date')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('finalized_at')->nullable();
            $table->json('line_items')->nullable();
            $table->json('tax_rates')->nullable();
            $table->json('discounts')->nullable();
            $table->json('metadata')->nullable();
            $table->json('history')->nullable();
            $table->string('pdf_url', 255)->nullable();
            // $table->foreign('user_id')->references('id')->on('users');
            // $table->foreign('subscription_id')->references('id')->on('subscriptions');
            $table->timestamps();
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

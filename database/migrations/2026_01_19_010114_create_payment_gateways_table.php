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
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('code', 255);
            $table->string('type', 255);
            $table->boolean('is_active')->default(false);
            $table->boolean('is_test_mode')->default(true);
            $table->boolean('supports_recurring')->default(false);
            $table->boolean('supports_refunds')->default(false);
            $table->boolean('supports_installments')->default(false);
            $table->text('api_key')->nullable();
            $table->text('api_secret')->nullable();
            $table->text('webhook_secret')->nullable();
            $table->text('merchant_id')->nullable();
            $table->text('store_id')->nullable();
            $table->text('store_password')->nullable();
            $table->string('base_url', 255)->nullable();
            $table->string('callback_url', 255)->nullable();
            $table->string('webhook_url', 255)->nullable();
            $table->json('supported_currencies')->nullable();
            $table->json('supported_countries')->nullable();
            $table->json('excluded_countries')->nullable();
            $table->decimal('percentage_fee', 5, 2)->default(0.00);
            $table->decimal('fixed_fee', 10, 2)->default(0.00);
            $table->string('fee_currency', 255)->default('USD');
            $table->json('fee_structure')->nullable();
            $table->json('config')->nullable();
            $table->json('metadata')->nullable();
            $table->string('settlement_days', 11)->default(2);
            $table->string('refund_days', 11)->default(5);
            $table->decimal('min_amount', 10, 2)->default(0.00);
            $table->decimal('max_amount', 20, 2)->default(999999.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};

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
            $table->string('name');
            $table->string('code', 100)->unique();
            $table->enum('type', ['card', 'wallet', 'bank', 'crypto', 'aggregator', 'cash']);
            $table->boolean('is_active')->default(0);
            $table->boolean('is_test_mode')->default(1);
            $table->boolean('supports_recurring')->default(0);
            $table->boolean('supports_refunds')->default(0);
            $table->boolean('supports_installments')->default(0);
            $table->text('api_key')->nullable();
            $table->text('api_secret')->nullable();
            $table->text('webhook_secret')->nullable();
            $table->text('merchant_id')->nullable();
            $table->text('store_id')->nullable();
            $table->text('store_password')->nullable();
            $table->string('base_url', 500)->nullable();
            $table->string('callback_url', 500)->nullable();
            $table->string('webhook_url', 500)->nullable();
            $table->json('supported_currencies')->nullable();
            $table->json('supported_countries')->nullable();
            $table->json('excluded_countries')->nullable();
            $table->decimal('percentage_fee', 5, 2)->default(0);
            $table->decimal('fixed_fee', 10, 2)->default(0);
            $table->char('fee_currency', 3)->default('USD');
            $table->json('fee_structure')->nullable();
            $table->json('config')->nullable();
            $table->json('metadata')->nullable();
            $table->integer('settlement_days')->default(2);
            $table->integer('refund_days')->default(5);
            $table->decimal('min_amount', 10, 2)->default(0);
            $table->decimal('max_amount', 20, 2)->default(999999);
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['code', 'is_active', 'is_test_mode'], 'active_gateways_idx');
            $table->index(['type', 'is_active'], 'gateway_type_idx');
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

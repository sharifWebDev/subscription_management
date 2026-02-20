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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('type', 255);
            $table->string('gateway', 255);
            $table->string('gateway_customer_id', 255)->nullable();
            $table->string('gateway_payment_method_id', 255)->nullable();
            $table->string('nickname', 255)->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->string('card_last4', 255)->nullable();
            $table->string('card_brand', 255)->nullable();
            $table->string('card_exp_month', 11)->nullable();
            $table->string('card_exp_year', 11)->nullable();
            $table->string('card_country', 255)->nullable();
            $table->string('bank_name', 255)->nullable();
            $table->string('bank_account_last4', 255)->nullable();
            $table->string('bank_account_type', 255)->nullable();
            $table->string('bank_routing_number', 255)->nullable();
            $table->string('wallet_type', 255)->nullable();
            $table->string('wallet_number', 255)->nullable();
            $table->string('crypto_currency', 255)->nullable();
            $table->string('crypto_address', 255)->nullable();
            $table->json('encrypted_data')->nullable();
            $table->string('fingerprint', 255)->nullable();
            $table->boolean('is_compromised')->default(false);
            $table->json('metadata')->nullable();
            $table->json('gateway_metadata')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->string('verified_by', 255)->nullable();
            $table->dateTime('last_used_at')->nullable();
            $table->string('usage_count', 11)->default(0);
            // $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};

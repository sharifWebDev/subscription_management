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
            $table->char('user_id', 36);
            $table->enum('type', ['card', 'bank_account', 'digital_wallet', 'crypto_wallet', 'cash', 'custom']);
            $table->string('gateway', 50);
            $table->string('gateway_customer_id', 255)->nullable();
            $table->string('gateway_payment_method_id', 255)->nullable();
            $table->string('nickname', 100)->nullable();
            $table->boolean('is_default')->default(0);
            $table->boolean('is_verified')->default(0);
            $table->char('card_last4', 4)->nullable();
            $table->string('card_brand', 20)->nullable();
            $table->integer('card_exp_month')->nullable();
            $table->integer('card_exp_year')->nullable();
            $table->char('card_country', 2)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->char('bank_account_last4', 4)->nullable();
            $table->string('bank_account_type', 20)->nullable();
            $table->string('bank_routing_number', 50)->nullable();
            $table->string('wallet_type', 50)->nullable();
            $table->string('wallet_number', 20)->nullable();
            $table->string('crypto_currency', 10)->nullable();
            $table->string('crypto_address', 255)->nullable();
            $table->json('encrypted_data')->nullable();
            $table->string('fingerprint', 255)->nullable();
            $table->boolean('is_compromised')->default(0);
            $table->json('metadata')->nullable();
            $table->json('gateway_metadata')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->char('verified_by', 36)->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->integer('usage_count')->default(0);
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'type', 'is_default'], 'user_payment_methods_idx');
            $table->index(['gateway', 'gateway_payment_method_id'], 'gateway_method_lookup_idx');
            $table->unique(['user_id', 'fingerprint'], 'unique_payment_method');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
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

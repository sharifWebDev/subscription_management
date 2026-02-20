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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('remember_token', 100)->nullable();
            $table->enum('billing_type', ['personal', 'business', 'enterprise'])->default('personal');
            $table->string('tax_id', 50)->nullable();
            $table->boolean('is_tax_exempt')->default(0);
            $table->json('tax_certificate')->nullable();
            $table->json('billing_address')->nullable();
            $table->json('shipping_address')->nullable();
            $table->char('preferred_currency', 3)->default('USD');
            $table->string('preferred_payment_method', 50)->nullable();
            $table->boolean('auto_renew')->default(1);
            $table->timestamp('trial_ends_at')->nullable();
            $table->boolean('has_used_trial')->default(0);
            $table->enum('account_status', ['active', 'suspended', 'closed', 'fraudulent'])->default('active');
            $table->text('account_status_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->json('preferences')->nullable();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['email', 'account_status'], 'user_email_status_idx');
            $table->index(['trial_ends_at', 'account_status'], 'user_trial_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

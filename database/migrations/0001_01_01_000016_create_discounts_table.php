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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique();
            $table->string('name');
            $table->enum('type', ['percentage', 'fixed', 'trial', 'usage']);
            $table->decimal('amount', 10, 4);
            $table->char('currency', 3)->nullable();
            $table->enum('applies_to', ['all', 'plans', 'features', 'users', 'subscriptions']);
            $table->json('applies_to_ids')->nullable();
            $table->integer('max_redemptions')->nullable();
            $table->integer('times_redeemed')->default(0);
            $table->boolean('is_active')->default(1);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->enum('duration', ['once', 'forever', 'repeating', 'subscription']);
            $table->integer('duration_in_months')->nullable();
            $table->json('metadata')->nullable();
            $table->json('restrictions')->nullable();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['code', 'is_active', 'expires_at'], 'active_discounts_idx');
            $table->index(['type', 'applies_to', 'starts_at'], 'discount_finder_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};

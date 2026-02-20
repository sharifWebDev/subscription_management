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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code', 100)->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['recurring', 'usage', 'one_time', 'hybrid'])->default('recurring');
            $table->enum('billing_period', ['monthly', 'yearly', 'quarterly', 'weekly', 'daily'])->default('monthly');
            $table->integer('billing_interval')->default(1);
            $table->boolean('is_active')->default(1);
            $table->boolean('is_visible')->default(1);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(0);
            $table->json('metadata')->nullable();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'is_active', 'is_visible'], 'plan_type_active_idx');
            $table->index(['sort_order', 'is_featured'], 'plan_display_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};

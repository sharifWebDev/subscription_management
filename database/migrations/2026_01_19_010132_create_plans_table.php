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
            $table->string('name', 255);
            $table->string('code', 255);
            $table->text('description')->nullable();
            $table->string('type', 255)->default('recurring');
            $table->string('billing_period', 255)->default('monthly');
            $table->string('billing_interval', 11)->default(1);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_visible')->default(true);
            $table->string('sort_order', 11)->default(0);
            $table->boolean('is_featured')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
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

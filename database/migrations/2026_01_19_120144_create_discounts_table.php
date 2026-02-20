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
            $table->string('code', 255);
            $table->string('name', 255);
            $table->string('type', 255);
            $table->decimal('amount', 10, 4);
            $table->string('currency', 255)->nullable();
            $table->string('applies_to', 255);
            $table->json('applies_to_ids')->nullable();
            $table->string('max_redemptions', 11)->nullable();
            $table->string('times_redeemed', 11)->default(0);
            $table->boolean('is_active')->default(true);
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->string('duration', 255);
            $table->string('duration_in_months', 11)->nullable();
            $table->json('metadata')->nullable();
            $table->json('restrictions')->nullable();
            $table->timestamps();
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

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
        Schema::create('plan_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('feature_id')->constrained('features', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('value', 255);
            $table->json('config')->nullable();
            $table->string('sort_order', 11)->default(0);
            $table->boolean('is_inherited')->default(false);
            $table->string('parent_feature_id', 255)->nullable();
            $table->dateTime('effective_from')->useCurrent();
            $table->dateTime('effective_to')->nullable();
            // $table->foreign('plan_id')->references('id')->on('plans');
            // $table->foreign('feature_id')->references('id')->on('features');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_features');
    }
};

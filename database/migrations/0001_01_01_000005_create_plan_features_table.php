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
            $table->char('plan_id', 36);
            $table->char('feature_id', 36);
            $table->string('value', 255);
            $table->json('config')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_inherited')->default(false);
            $table->char('parent_feature_id', 36)->nullable();
            $table->timestamp('effective_from')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('effective_to')->nullable();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->timestamp('deleted_at')->nullable();

            $table->unique(['plan_id', 'feature_id'], 'plan_feature_unique');
            $table->index(['feature_id', 'value'], 'feature_value_idx');

            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('feature_id')->references('id')->on('features')->onDelete('cascade')->onUpdate('cascade');
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

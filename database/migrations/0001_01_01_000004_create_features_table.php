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
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('code', 100);
            $table->text('description')->nullable();
            $table->enum('type', ['limit', 'boolean', 'tiered'])->default('limit');
            $table->enum('scope', ['global', 'per_user', 'per_seat', 'per_team'])->default('global');
            $table->boolean('is_resettable')->default(false);
            $table->enum('reset_period', ['monthly', 'yearly', 'weekly', 'never'])->default('monthly');
            $table->json('metadata')->nullable();
            $table->json('validations')->nullable();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->timestamp('deleted_at')->nullable();

            $table->unique('code');
            $table->index(['code', 'type'], 'feature_code_type_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('features');
    }
};

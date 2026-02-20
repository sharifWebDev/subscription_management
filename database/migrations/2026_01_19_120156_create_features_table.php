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
            $table->string('code', 255);
            $table->text('description')->nullable();
            $table->string('type', 255)->default('limit');
            $table->string('scope', 255)->default('global');
            $table->boolean('is_resettable')->default(false);
            $table->string('reset_period', 255)->default('monthly');
            $table->json('metadata')->nullable();
            $table->json('validations')->nullable();
            $table->timestamps();
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

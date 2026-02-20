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
        Schema::create('hk_prod_uoms', function (Blueprint $table) {
            $table->id();
            $table->string('code', 255);
            $table->string('name', 255);
            $table->boolean('is_active')->default(false);
            $table->bigInteger('sequence')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hk_prod_uoms');
    }
};

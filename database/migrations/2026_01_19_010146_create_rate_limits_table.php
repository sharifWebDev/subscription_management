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
        Schema::create('rate_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('feature_id')->constrained('features', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('key', 255);
            $table->string('max_attempts', 11);
            $table->string('decay_seconds', 11);
            $table->string('remaining', 11);
            $table->dateTime('resets_at');
            // $table->foreign('subscription_id')->references('id')->on('subscriptions');
            // $table->foreign('feature_id')->references('id')->on('features');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_limits');
    }
};

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
        Schema::create('subscription_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('type', 255);
            $table->json('data')->nullable();
            $table->json('changes')->nullable();
            $table->string('causer_id', 255)->nullable();
            $table->string('causer_type', 255)->nullable();
            $table->string('ip_address', 255)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->dateTime('occurred_at')->useCurrent();
            // $table->foreign('subscription_id')->references('id')->on('subscriptions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_events');
    }
};

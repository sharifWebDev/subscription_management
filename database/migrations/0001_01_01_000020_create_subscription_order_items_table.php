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
        Schema::create('subscription_order_items', function (Blueprint $table) {
            $table->id();
            $table->char('subscription_order_id', 36);
            $table->char('plan_id', 36);
            $table->char('user_id', 36);
            $table->string('plan_name');
            $table->string('billing_cycle', 20);
            $table->integer('quantity')->default(1);
            $table->char('recipient_user_id', 36)->nullable();
            $table->json('recipient_info')->nullable();
            $table->decimal('unit_price', 20, 8);
            $table->decimal('amount', 20, 8);
            $table->decimal('tax_amount', 20, 8)->default(0);
            $table->decimal('discount_amount', 20, 8)->default(0);
            $table->decimal('total_amount', 20, 8);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->char('subscription_id', 36)->nullable();
            $table->enum('subscription_status', ['pending', 'created', 'failed'])->default('pending');
            $table->text('processing_error')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['subscription_order_id', 'plan_id'], 'order_items_idx');
            $table->index(['user_id', 'subscription_id'], 'user_order_subscriptions_idx');
            $table->index(['recipient_user_id', 'subscription_status'], 'gifted_subscriptions_idx');

            $table->foreign('subscription_order_id')->references('id')->on('subscription_orders')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('recipient_user_id')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_order_items');
    }
};

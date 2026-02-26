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
        Schema::table('payment_transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('payment_transactions', 'gateway_transaction_id')) {
                $table->string('gateway_transaction_id')->nullable()->after('transaction_id');
                $table->index('gateway_transaction_id');
            }

            // Also check for other missing columns that might be needed
            if (! Schema::hasColumn('payment_transactions', 'failed_at')) {
                $table->timestamp('failed_at')->nullable()->after('completed_at');
            }

            if (! Schema::hasColumn('payment_transactions', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('failed_at');
            }

            if (! Schema::hasColumn('payment_transactions', 'failure_reason')) {
                $table->text('failure_reason')->nullable()->after('cancelled_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'gateway_transaction_id',
                'failed_at',
                'cancelled_at',
                'failure_reason',
            ]);
        });
    }
};

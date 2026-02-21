<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $table = 'payment_transactions';

    protected $fillable = ['payment_master_id', 'payment_child_id', 'transaction_id', 'reference_id', 'type',
        'payment_method', 'payment_gateway', 'gateway_response', 'payment_method_details', 'amount', 'fee', 'tax',
        'net_amount', 'currency', 'exchange_rate', 'status', 'card_last4', 'card_brand', 'card_country', 'card_exp_month',
        'card_exp_year', 'bank_name', 'bank_account_last4', 'bank_routing_number', 'wallet_type', 'wallet_number',
        'wallet_transaction_id', 'installment_number', 'total_installments', 'initiated_at', 'authorized_at',
        'captured_at', 'completed_at', 'failed_at', 'refunded_at', 'fraud_indicators', 'risk_score',
        'requires_review', 'metadata', 'custom_fields', 'notes', 'failure_reason', 'ip_address', 'user_agent',
        'location_data', 'created_by', 'updated_by'];

    // cast
    protected $casts = [
        'fraud_indicators' => 'array',
        'metadata' => 'array',
        'custom_fields' => 'array',
        'captured_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime',
        'requires_review' => 'boolean',
        'risk_score' => 'integer',
        'created_by' => 'integer',
        'initiated_at' => 'datetime',
        'authorized_at' => 'datetime',
        'refunded_at' => 'datetime',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function paymentMaster()
    {
        return $this->belongsTo(\App\Models\PaymentMaster::class, 'payment_master_id');
    }

    public function paymentChild()
    {
        return $this->belongsTo(\App\Models\PaymentChild::class, 'payment_child_id');
    }

    public function paymentWebhookLogs()
    {
        return $this->hasMany(\App\Models\PaymentWebhookLog::class, 'payment_transaction_id');
    }

    public function refunds()
    {
        return $this->hasMany(\App\Models\Refund::class, 'payment_transaction_id');
    }
}

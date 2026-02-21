<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentWebhookLog extends Model
{
    use HasFactory;

    protected $table = 'payment_webhook_logs';

    protected $fillable = ['payment_gateway_id', 'payment_transaction_id', 'gateway', 'event_type', 'webhook_id',
        'reference_id', 'payload', 'headers', 'response_code', 'response_body', 'status', 'processing_error',
        'retry_count', 'next_retry_at', 'received_at', 'processed_at', 'ip_address', 'is_verified', 'verification_error',
        'created_by', 'updated_by'];

    // cast
    protected $casts = [
        'next_retry_at' => 'datetime',
        'received_at' => 'datetime',
        'processed_at' => 'datetime',
        'is_verified' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function paymentGateway()
    {
        return $this->belongsTo(\App\Models\PaymentGateway::class, 'payment_gateway_id');
    }

    public function paymentTransaction()
    {
        return $this->belongsTo(\App\Models\PaymentTransaction::class, 'payment_transaction_id');
    }
}

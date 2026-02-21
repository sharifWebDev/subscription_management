<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $table = 'payment_gateways';

    protected $fillable = ['name', 'code', 'type', 'is_active', 'is_test_mode', 'supports_recurring',
        'supports_refunds', 'supports_installments', 'api_key', 'api_secret', 'webhook_secret', 'merchant_id',
        'store_id', 'store_password', 'base_url', 'callback_url', 'webhook_url', 'supported_currencies',
        'supported_countries', 'excluded_countries', 'percentage_fee', 'fixed_fee', 'fee_currency',
        'fee_structure', 'config', 'metadata', 'settlement_days', 'refund_days', 'min_amount', 'max_amount',
        'created_by', 'updated_by'];

    // cast
    protected $casts = [
        'is_active' => 'boolean',
        'is_test_mode' => 'boolean',
        'supports_recurring' => 'boolean',
        'supports_refunds' => 'boolean',
        'supports_installments' => 'boolean',
        'percentage_fee' => 'float',
        'is_featured' => 'boolean',
        'fixed_fee' => 'float',
        'settlement_days' => 'integer',
        'refund_days' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function paymentWebhookLogs()
    {
        return $this->hasMany(\App\Models\PaymentWebhookLog::class, 'payment_gateway_id');
    }
}

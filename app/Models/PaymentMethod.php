<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'payment_methods';

    protected $fillable = ['user_id', 'type', 'gateway', 'gateway_customer_id', 'gateway_payment_method_id',
        'nickname', 'is_default', 'is_verified', 'card_last4', 'card_brand', 'card_exp_month', 'card_exp_year',
        'card_country', 'bank_name', 'bank_account_last4', 'bank_account_type', 'bank_routing_number', 'wallet_type',
        'wallet_number', 'crypto_currency', 'crypto_address', 'encrypted_data', 'fingerprint', 'is_compromised',
        'metadata', 'gateway_metadata', 'verified_at', 'verified_by', 'last_used_at', 'usage_count', 'created_by',
        'updated_by'];

    // cast
    protected $casts = [
        'verified_at' => 'datetime',
        'last_used_at' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMaster extends Model
{
    use HasFactory;

    protected $table = 'payment_masters';

    protected $fillable = ['user_id', 'payment_number', 'type', 'status', 'total_amount', 'subtotal', 'tax_amount',
    'discount_amount', 'fee_amount', 'net_amount', 'paid_amount', 'due_amount', 'currency', 'exchange_rate',
     'base_currency', 'base_amount', 'payment_method', 'payment_method_details', 'payment_gateway',
      'is_installment', 'installment_count', 'installment_frequency', 'payment_date', 'due_date', 'paid_at',
      'cancelled_at', 'expires_at', 'customer_reference', 'bank_reference', 'gateway_reference', 'metadata',
       'custom_fields', 'notes', 'failure_reason', 'created_by', 'updated_by'];

       //cast
    protected $casts = [
        'user_id' => 'integer',
        'payment_number' => 'string',
        'type' => 'string',
        'status' => 'string',
        'payment_date' => 'datetime',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function paymentTransactions()
    {
        return $this->hasMany(\App\Models\PaymentTransaction::class, 'payment_master_id');
    }

    public function refunds()
    {
        return $this->hasMany(\App\Models\Refund::class, 'payment_master_id');
    }

    public function subscriptionOrders()
    {
        return $this->hasMany(\App\Models\SubscriptionOrder::class, 'payment_master_id');
    }
}

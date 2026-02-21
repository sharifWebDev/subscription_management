<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionOrder extends Model
{
    use HasFactory;

    protected $table = 'subscription_orders';

    protected $fillable = ['user_id', 'payment_master_id', 'order_number', 'status', 'type', 'subtotal', 'tax_amount',
        'discount_amount', 'total_amount', 'currency', 'customer_info', 'billing_address', 'ordered_at',
        'processed_at', 'cancelled_at', 'coupon_code', 'applied_discounts', 'metadata', 'notes', 'failure_reason',
        'created_by', 'updated_by'];

    // cast
    protected $casts = [
        'ordered_at' => 'datetime',
        'processed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function paymentMaster()
    {
        return $this->belongsTo(\App\Models\PaymentMaster::class, 'payment_master_id');
    }
}

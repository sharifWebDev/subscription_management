<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionOrderItem extends Model
{
    use HasFactory;

    protected $table = 'subscription_order_items';

    protected $fillable = ['subscription_order_id', 'plan_id', 'user_id', 'recipient_user_id', 'subscription_id',
        'plan_name', 'billing_cycle', 'quantity', 'recipient_info', 'unit_price', 'amount', 'tax_amount',
        'discount_amount', 'total_amount', 'start_date', 'end_date', 'subscription_status', 'processing_error',
        'processed_at', 'created_by', 'updated_by'];

    // cast
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'subscription_order_id' => 'integer',
        'plan_id' => 'integer',
        'processed_at' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function subscriptionOrder()
    {
        return $this->belongsTo(\App\Models\SubscriptionOrder::class, 'subscription_order_id');
    }

    public function plan()
    {
        return $this->belongsTo(\App\Models\Plan::class, 'plan_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // public function user()
    // {
    //     return $this->belongsTo(\App\Models\User::class, 'recipient_user_id');
    // }

    public function subscription()
    {
        return $this->belongsTo(\App\Models\Subscription::class, 'subscription_id');
    }

    // order
    public function order()
    {
        return $this->belongsTo(\App\Models\SubscriptionOrder::class, 'subscription_order_id');
    }
}

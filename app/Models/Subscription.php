<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';

    protected $fillable = ['user_id', 'plan_id', 'plan_price_id', 'parent_subscription_id', 'status',
        'billing_cycle_anchor', 'quantity', 'unit_price', 'amount', 'currency', 'trial_starts_at',
        'trial_ends_at', 'trial_converted', 'current_period_starts_at', 'current_period_ends_at',
        'billing_cycle_anchor_date', 'canceled_at', 'cancellation_reason', 'prorate', 'proration_amount',
        'proration_date', 'gateway', 'gateway_subscription_id', 'gateway_customer_id', 'gateway_metadata',
        'metadata', 'history', 'created_by', 'updated_by', 'is_active'];

    // cast
    protected $casts = [
        'billing_cycle_anchor_date' => 'datetime',
        'canceled_at' => 'datetime',
        'trial_starts_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'current_period_starts_at' => 'datetime',
        'current_period_ends_at' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function plan()
    {
        return $this->belongsTo(\App\Models\Plan::class, 'plan_id');
    }

    public function planPrice()
    {
        return $this->belongsTo(\App\Models\PlanPrice::class, 'plan_price_id');
    }

    public function usageRecords()
    {
        return $this->hasMany(\App\Models\UsageRecord::class, 'subscription_id');
    }

    public function invoices()
    {
        return $this->hasMany(\App\Models\Invoice::class, 'subscription_id');
    }

    // items
    public function items()
    {
        return $this->hasMany(\App\Models\SubscriptionItem::class, 'subscription_id');
    }

    // events
    public function events()
    {
        return $this->hasMany(\App\Models\SubscriptionEvent::class, 'subscription_id');
    }

    // prices
    public function price()
    {
        return $this->belongsTo(\App\Models\PlanPrice::class, 'plan_price_id');
    }

    // payments
    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class, 'subscription_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanPrice extends Model
{
    use HasFactory;

    protected $table = 'plan_prices';

    protected $fillable = ['plan_id', 'currency', 'amount', 'interval', 'interval_count', 'usage_type', 'tiers',
    'transformations', 'stripe_price_id', 'active_from', 'active_to', 'created_by', 'updated_by'];

       //cast
    protected $casts = [
        'active_from' => 'datetime',
        'active_to' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(\App\Models\Plan::class, 'plan_id');
    }

    public function subscriptionItems()
    {
        return $this->hasMany(\App\Models\SubscriptionItem::class, 'plan_price_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(\App\Models\Subscription::class, 'plan_price_id');
    }
}

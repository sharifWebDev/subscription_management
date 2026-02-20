<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionItem extends Model
{
    use HasFactory;

    protected $table = 'subscription_items';

    protected $fillable = ['subscription_id', 'plan_price_id', 'feature_id', 'quantity', 'unit_price', 'amount',
     'metadata', 'tiers', 'effective_from', 'effective_to', 'created_by', 'updated_by'];

   //cast
    protected $casts = [
        'effective_from' => 'datetime',
        'effective_to' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function subscription()
    {
        return $this->belongsTo(\App\Models\Subscription::class, 'subscription_id');
    }

    public function planPrice()
    {
        return $this->belongsTo(\App\Models\PlanPrice::class, 'plan_price_id');
    }

    public function feature()
    {
        return $this->belongsTo(\App\Models\Feature::class, 'feature_id');
    }

    public function usageRecords()
    {
        return $this->hasMany(\App\Models\UsageRecord::class, 'subscription_item_id');
    }
}

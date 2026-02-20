<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsageRecord extends Model
{
    use HasFactory;

    protected $table = 'usage_records';

    protected $fillable = ['subscription_id', 'subscription_item_id', 'feature_id', 'quantity', 'tier_quantity',
     'amount', 'unit', 'status', 'recorded_at', 'billing_date', 'metadata', 'dimensions', 'created_by', 'updated_by'];

       //cast
    protected $casts = [
        'dimensions' => 'array',
        'recorded_at' => 'datetime',
        'billing_date' => 'date', 
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function subscription()
    {
        return $this->belongsTo(\App\Models\Subscription::class, 'subscription_id');
    }

    public function subscriptionItem()
    {
        return $this->belongsTo(\App\Models\SubscriptionItem::class, 'subscription_item_id');
    }

    public function feature()
    {
        return $this->belongsTo(\App\Models\Feature::class, 'feature_id');
    }
}

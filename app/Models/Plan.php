<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'plans';

    protected $fillable = ['name', 'slug', 'code', 'description', 'type', 'billing_period', 'billing_interval',
     'is_active', 'is_visible', 'sort_order', 'is_featured', 'metadata', 'created_by', 'updated_by'];

   //cast
    protected $casts = [
        'billing_period' => 'integer',
        'billing_interval' => 'integer',
        'metadata' => 'array',
        'is_active' => 'boolean',
        'is_visible' => 'boolean',
        'is_featured' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function subscriptionOrderItems()
    {
        return $this->hasMany(\App\Models\SubscriptionOrderItem::class, 'plan_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(\App\Models\Subscription::class, 'plan_id');
    }
}

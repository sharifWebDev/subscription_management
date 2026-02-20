<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionEvent extends Model
{
    use HasFactory;

    protected $table = 'subscription_events';

    protected $fillable = ['subscription_id', 'type', 'data', 'changes', 'causer_id', 'causer_type', 'ip_address',
     'user_agent', 'metadata', 'occurred_at', 'created_by', 'updated_by'];

   //cast
    protected $casts = [
        'occurred_at' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function subscription()
    {
        return $this->belongsTo(\App\Models\Subscription::class, 'subscription_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RateLimit extends Model
{
    use HasFactory;

    protected $table = 'rate_limits';

    protected $fillable = ['subscription_id', 'feature_id', 'key', 'max_attempts', 'decay_seconds', 'remaining',
     'resets_at', 'created_by', 'updated_by'];

       //cast
    protected $casts = [
        'resets_at' => 'datetime',

        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function subscription()
    {
        return $this->belongsTo(\App\Models\Subscription::class, 'subscription_id');
    }

    public function feature()
    {
        return $this->belongsTo(\App\Models\Feature::class, 'feature_id');
    }
}

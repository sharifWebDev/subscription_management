<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeteredUsageAggregate extends Model
{
    use HasFactory;

    protected $table = 'metered_usage_aggregates';

    protected $fillable = ['subscription_id', 'feature_id', 'aggregate_date', 'aggregate_period', 'total_quantity',
     'tier1_quantity', 'tier2_quantity', 'tier3_quantity', 'total_amount', 'record_count', 'last_calculated_at',
      'created_by', 'updated_by'];

       //cast
    protected $casts = [
        'aggregate_date' => 'datetime',
        'last_calculated_at' => 'datetime',
        'subscription_id' => 'integer',
        'feature_id' => 'integer',  
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

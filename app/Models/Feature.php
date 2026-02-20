<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;

    protected $table = 'features';

    protected $fillable = ['name', 'code', 'description', 'type', 'scope', 'is_resettable', 'reset_period',
    'metadata', 'validations', 'created_by', 'updated_by'];

    //cast
    protected $casts = [
        'updated_by' => 'integer',
        'created_at' => 'datetime'
    ];

    // handleFileUpload

    public function meteredUsageAggregates()
    {
        return $this->hasMany(\App\Models\MeteredUsageAggregate::class, 'feature_id');
    }

    public function planFeatures()
    {
        return $this->hasMany(\App\Models\PlanFeature::class, 'feature_id');
    }

    public function rateLimits()
    {
        return $this->hasMany(\App\Models\RateLimit::class, 'feature_id');
    }

    public function subscriptionItems()
    {
        return $this->hasMany(\App\Models\SubscriptionItem::class, 'feature_id');
    }

    public function usageRecords()
    {
        return $this->hasMany(\App\Models\UsageRecord::class, 'feature_id');
    }
}

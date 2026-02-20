<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanFeature extends Model
{
    use HasFactory;

    protected $table = 'plan_features';

    protected $fillable = ['plan_id', 'feature_id', 'value', 'config', 'sort_order', 'is_inherited',
    'parent_feature_id', 'effective_from', 'effective_to', 'created_by', 'updated_by'];

       //cast
    protected $casts = [
        'effective_from' => 'datetime',
        'effective_to' => 'datetime',
        'plan_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(\App\Models\Plan::class, 'plan_id');
    }

    public function feature()
    {
        return $this->belongsTo(\App\Models\Feature::class, 'feature_id');
    }
}

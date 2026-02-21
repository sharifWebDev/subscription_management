<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanFeature extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'plan_features';

    protected $fillable = [
        'plan_id',
        'feature_id',
        'value',
        'config',
        'sort_order',
        'is_inherited',
        'parent_feature_id',
        'effective_from',
        'effective_to',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'config' => 'array',
        'sort_order' => 'integer',
        'is_inherited' => 'boolean',
        'effective_from' => 'datetime',
        'effective_to' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the plan that owns the feature
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the feature
     */
    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }

    /**
     * Get the parent feature
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(PlanFeature::class, 'parent_feature_id');
    }

    /**
     * Scope a query to only include active features
     */
    public function scopeActive($query)
    {
        return $query->where('effective_from', '<=', now())
            ->where(function ($q) {
                $q->whereNull('effective_to')
                    ->orWhere('effective_to', '>', now());
            });
    }

    /**
     * Check if feature is unlimited
     */
    public function isUnlimited(): bool
    {
        return strtolower($this->value) === 'unlimited';
    }

    /**
     * Get numeric value
     */
    public function getNumericValue()
    {
        if ($this->isUnlimited()) {
            return PHP_FLOAT_MAX;
        }

        return is_numeric($this->value) ? (float) $this->value : 0;
    }
}

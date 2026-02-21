<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'plans';

    protected $fillable = [
        'name',
        'slug',
        'code',
        'description',
        'type',
        'billing_period',
        'billing_interval',
        'is_active',
        'is_visible',
        'sort_order',
        'is_featured',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_visible' => 'boolean',
        'is_featured' => 'boolean',
        'billing_interval' => 'integer',
        'sort_order' => 'integer',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'type' => 'recurring',
        'billing_period' => 'monthly',
        'billing_interval' => 1,
    ];

    // make slug uri
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the features for the plan
     */
    public function planFeatures(): HasMany
    {
        return $this->hasMany(PlanFeature::class);
    }

    /**
     * Get the prices for the plan
     */
    public function prices(): HasMany
    {
        return $this->hasMany(PlanPrice::class);
    }

    /**
     * Get the discounts for the plan
     */
    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'plan_discounts')
            ->withPivot('created_by', 'updated_by')
            ->withTimestamps();
    }

    /**
     * Get active prices
     */
    public function activePrices()
    {
        return $this->prices()
            ->where(function ($query) {
                $query->whereNull('active_to')
                    ->orWhere('active_to', '>', now());
            })
            ->where('active_from', '<=', now());
    }

    /**
     * Get active features
     */
    public function activeFeatures()
    {
        return $this->planFeatures()
            ->where(function ($query) {
                $query->whereNull('effective_to')
                    ->orWhere('effective_to', '>', now());
            })
            ->where('effective_from', '<=', now())
            ->orderBy('sort_order');
    }

    /**
     * Scope a query to only include active plans
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include visible plans
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope a query to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get the plan's price for a specific currency
     */
    public function getPrice($currency = 'USD')
    {
        return $this->activePrices()
            ->where('currency', $currency)
            ->first();
    }

    /**
     * Check if plan has feature
     */
    public function hasFeature($featureCode)
    {
        return $this->activeFeatures()
            ->whereHas('feature', function ($query) use ($featureCode) {
                $query->where('code', $featureCode);
            })
            ->exists();
    }

    /**
     * Get feature value
     */
    public function getFeatureValue($featureCode)
    {
        $planFeature = $this->activeFeatures()
            ->whereHas('feature', function ($query) use ($featureCode) {
                $query->where('code', $featureCode);
            })
            ->first();

        return $planFeature ? $planFeature->value : null;
    }

    /**
     * Generate slug from name
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($plan) {
            if (empty($plan->slug)) {
                $plan->slug = \Str::slug($plan->name);
            }
            if (empty($plan->code)) {
                $plan->code = strtoupper(\Str::slug($plan->name, '_'));
            }
        });

        static::updating(function ($plan) {
            if ($plan->isDirty('name') && empty($plan->slug)) {
                $plan->slug = \Str::slug($plan->name);
            }
        });
    }
}

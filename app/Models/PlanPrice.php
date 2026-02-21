<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanPrice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'plan_prices';

    protected $fillable = [
        'plan_id',
        'currency',
        'amount',
        'interval',
        'interval_count',
        'usage_type',
        'tiers',
        'transformations',
        'stripe_price_id',
        'active_from',
        'active_to',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'interval_count' => 'integer',
        'tiers' => 'array',
        'transformations' => 'array',
        'active_from' => 'datetime',
        'active_to' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'currency' => 'USD',
        'interval' => 'month',
        'interval_count' => 1,
        'usage_type' => 'licensed',
    ];

    /**
     * Get the plan that owns the price
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Scope a query to only include active prices
     */
    public function scopeActive($query)
    {
        return $query->where('active_from', '<=', now())
            ->where(function ($q) {
                $q->whereNull('active_to')
                    ->orWhere('active_to', '>', now());
            });
    }

    /**
     * Scope a query to filter by currency
     */
    public function scopeCurrency($query, $currency)
    {
        return $query->where('currency', $currency);
    }

    /**
     * Scope a query to filter by interval
     */
    public function scopeInterval($query, $interval)
    {
        return $query->where('interval', $interval);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmount(): string
    {
        return number_format($this->amount, 2);
    }

    /**
     * Get amount with currency symbol
     */
    public function getAmountWithCurrency(): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'BDT' => '৳',
        ];

        $symbol = $symbols[$this->currency] ?? $this->currency;

        return $symbol.' '.$this->getFormattedAmount();
    }

    /**
     * Get interval description
     */
    public function getIntervalDescription(): string
    {
        if ($this->interval_count === 1) {
            return 'per '.$this->interval;
        }

        return 'every '.$this->interval_count.' '.$this->interval.'s';
    }

    /**
     * Calculate price for quantity
     */
    public function calculatePrice($quantity = 1): float
    {
        if ($this->usage_type === 'tiered' && $this->tiers) {
            return $this->calculateTieredPrice($quantity);
        }

        return (float) $this->amount * $quantity;
    }

    /**
     * Calculate tiered price
     */
    protected function calculateTieredPrice($quantity): float
    {
        $total = 0;
        $remaining = $quantity;

        foreach ($this->tiers as $tier) {
            if ($remaining <= 0) {
                break;
            }

            $tierQuantity = $tier['last'] === null
                ? $remaining
                : min($remaining, $tier['last'] - $tier['first'] + 1);

            $total += $tierQuantity * $tier['price'];
            $remaining -= $tierQuantity;
        }

        return $total;
    }
}

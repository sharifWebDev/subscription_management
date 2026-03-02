<?php

namespace App\Models;

use App\Models\Discount;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanDiscount extends Pivot
{
    use SoftDeletes;

    protected $table = 'plan_discounts';

    protected $fillable = [
        'plan_id',
        'discount_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the plan
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the discount
     */
    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
}

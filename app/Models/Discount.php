<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $table = 'discounts';

    protected $fillable = ['code', 'name', 'type', 'amount', 'currency', 'applies_to', 'applies_to_ids',
     'max_redemptions', 'times_redeemed', 'is_active', 'starts_at', 'expires_at', 'duration',
     'duration_in_months', 'metadata', 'restrictions', 'created_by', 'updated_by'];

    // cast
    protected $casts = [
        'applies_to_ids' => 'array',
        // 'metadata' => 'array',
        // 'restrictions' => 'array',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime'
    ];

}

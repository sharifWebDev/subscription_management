<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentChild extends Model
{
    use HasFactory;

    protected $table = 'payment_children';

    protected $fillable = ['payment_master_id', 'subscription_id', 'plan_id', 'invoice_id', 'item_type',
        'item_id', 'description', 'item_code', 'unit_price', 'quantity', 'amount', 'tax_amount', 'discount_amount',
        'total_amount', 'period_start', 'period_end', 'billing_cycle', 'status', 'paid_at', 'allocated_amount',
        'is_fully_allocated', 'metadata', 'tax_breakdown', 'discount_breakdown', 'created_by', 'updated_by'];

    // cast
    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'paid_at' => 'date',
        'is_fully_allocated' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
    ];

    public function paymentMaster()
    {
        return $this->belongsTo(\App\Models\PaymentMaster::class, 'payment_master_id');
    }

    public function subscription()
    {
        return $this->belongsTo(\App\Models\Subscription::class, 'subscription_id');
    }

    public function plan()
    {
        return $this->belongsTo(\App\Models\Plan::class, 'plan_id');
    }

    public function invoice()
    {
        return $this->belongsTo(\App\Models\Invoice::class, 'invoice_id');
    }

    public function paymentTransactions()
    {
        return $this->hasMany(\App\Models\PaymentTransaction::class, 'payment_child_id');
    }
}

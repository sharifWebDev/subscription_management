<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentAllocation extends Model
{
    use HasFactory;

    protected $table = 'payment_allocations';

    protected $fillable = ['payment_master_id', 'payment_child_id', 'payment_transaction_id', 'allocatable_type',
        'allocatable_id', 'amount', 'base_amount', 'exchange_rate', 'currency', 'allocation_reference',
        'allocation_type', 'is_reversed', 'reversed_at', 'reversal_id', 'metadata', 'notes', 'created_by', 'updated_by'];

    // cast
    protected $casts = [
        'is_reversed' => 'boolean',
        'allocatable_id' => 'integer',
        'metadata' => 'array',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function paymentMaster()
    {
        return $this->belongsTo(\App\Models\PaymentMaster::class, 'payment_master_id');
    }

    public function paymentChild()
    {
        return $this->belongsTo(\App\Models\PaymentChild::class, 'payment_child_id');
    }

    public function paymentTransaction()
    {
        return $this->belongsTo(\App\Models\PaymentTransaction::class, 'payment_transaction_id');
    }
}

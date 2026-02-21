<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = ['invoice_id', 'user_id', 'external_id', 'type', 'status', 'amount', 'fee', 'net',
        'currency', 'gateway', 'gateway_response', 'payment_method', 'processed_at', 'refunded_at', 'metadata',
        'fraud_indicators', 'created_by', 'updated_by'];

    // cast
    protected $casts = [
        'processed_at' => 'datetime',
        'refunded_at' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(\App\Models\Invoice::class, 'invoice_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}

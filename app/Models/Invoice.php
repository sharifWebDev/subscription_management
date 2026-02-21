<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    protected $fillable = ['user_id', 'subscription_id', 'number', 'external_id', 'type', 'status', 'subtotal',
        'tax', 'total', 'amount_due', 'amount_paid', 'amount_remaining', 'currency', 'issue_date', 'due_date',
        'paid_at', 'finalized_at', 'line_items', 'tax_rates', 'discounts', 'metadata', 'history', 'pdf_url',
        'created_by', 'updated_by'];

    // cast
    protected $casts = [
        'user_id' => 'integer',
        'subscription_id' => 'integer',
        'number' => 'integer',
        'external_id' => 'string',
        'type' => 'string',
        'status' => 'string',
        'subtotal' => 'float',
        'tax' => 'float',
        'total' => 'float',
        'amount_due' => 'float',
        'amount_paid' => 'float',
        'amount_remaining' => 'float',
        'currency' => 'string',
        'issue_date' => 'datetime',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
        'finalized_at' => 'datetime',
        // 'line_items' => 'array',
        // 'tax_rates' => 'json',
        'discounts' => 'array',
        // 'metadata' => 'array',
        // 'history' => 'array',
        'pdf_url' => 'string',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function subscription()
    {
        return $this->belongsTo(\App\Models\Subscription::class, 'subscription_id');
    }

    // paymentMaster
    public function paymentMaster()
    {
        return $this->belongsTo(\App\Models\PaymentMaster::class, 'payment_master_id');
    }
}

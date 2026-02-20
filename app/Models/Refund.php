<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $table = 'refunds';

    protected $fillable = ['payment_master_id', 'payment_transaction_id', 'user_id', 'refund_number', 'type',
    'status', 'initiated_by', 'amount', 'fee', 'net_amount', 'currency', 'exchange_rate', 'reason', 'reason_details',
     'customer_comments', 'requested_at', 'approved_at', 'approved_by', 'processed_at', 'completed_at', 'failed_at',
     'gateway_refund_id', 'gateway_response', 'metadata', 'documents', 'processed_by', 'rejection_reason',
      'created_by', 'updated_by'];

       //cast
    protected $casts = [
        'amount' => 'float',
        'fee' => 'float',
        'net_amount' => 'float',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function paymentMaster()
    {
        return $this->belongsTo(\App\Models\PaymentMaster::class, 'payment_master_id');
    }

    public function paymentTransaction()
    {
        return $this->belongsTo(\App\Models\PaymentTransaction::class, 'payment_transaction_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}

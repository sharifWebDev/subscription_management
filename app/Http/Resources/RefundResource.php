<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class RefundResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payment_master_id' => $this->payment_master_id,
            'payment_master_id_details' => $this->PaymentMaster,
            'payment_master_name' => $this->PaymentMaster?->name ?? $this->PaymentMaster?->title ?? $this->PaymentMaster?->code ?? null,
            'payment_transaction_id' => $this->payment_transaction_id,
            'payment_transaction_id_details' => $this->PaymentTransaction,
            'payment_transaction_name' => $this->PaymentTransaction?->name ?? $this->PaymentTransaction?->title ?? $this->PaymentTransaction?->code ?? null,
            'user_id' => $this->user_id,
            'user_id_details' => $this->User,
            'user_name' => $this->User?->name ?? $this->User?->title ?? $this->User?->code ?? null,
            'refund_number' => $this->refund_number,
            'type' => $this->type,
            'status' => $this->status,
            'initiated_by' => $this->initiated_by,
            'amount' => $this->amount ? (float) $this->amount : 0.0,
            'fee' => $this->fee ? (float) $this->fee : 0.0,
            'net_amount' => $this->net_amount ? (float) $this->net_amount : 0.0,
            'currency' => $this->currency,
            'exchange_rate' => $this->exchange_rate ? (float) $this->exchange_rate : 0.0,
            'reason' => $this->reason,
            'reason_details' => $this->reason_details,
            'customer_comments' => $this->customer_comments,
            'requested_at' => $this->requested_at?->format('Y-m-d H:i:s'),
            'approved_at' => $this->approved_at?->format('Y-m-d H:i:s'),
            'approved_by' => $this->approved_by,
            'processed_at' => $this->processed_at?->format('Y-m-d H:i:s'),
            'completed_at' => $this->completed_at?->format('Y-m-d H:i:s'),
            'failed_at' => $this->failed_at?->format('Y-m-d H:i:s'),
            'gateway_refund_id' => $this->gateway_refund_id,
            'gateway_response' => $this->gateway_response ? json_decode($this->gateway_response, true) : [],
            'metadata' => $this->metadata ? json_decode($this->metadata, true) : [],
            'documents' => $this->documents ? json_decode($this->documents, true) : [],
            'processed_by' => $this->processed_by,
            'rejection_reason' => $this->rejection_reason,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at?->format('M d, Y h:i A'),
            'updated_at_formatted' => $this->updated_at?->format('M d, Y h:i A'),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'updated_at_human' => $this->updated_at?->diffForHumans(),
        ];
    }
}

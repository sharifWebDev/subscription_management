<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PaymentMasterResource extends BaseResource
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
            'user_id' => $this->user_id,
            'user_id_details' => $this->User,
            'user_name' => $this->User?->name ?? $this->User?->title ?? $this->User?->code ?? null,
            'payment_number' => $this->payment_number,
            'type' => $this->type,
            'status' => $this->status,
            'total_amount' => $this->total_amount ? (float) $this->total_amount : 0.0,
            'subtotal' => $this->subtotal ? (float) $this->subtotal : 0.0,
            'tax_amount' => $this->tax_amount ? (float) $this->tax_amount : 0.0,
            'discount_amount' => $this->discount_amount ? (float) $this->discount_amount : 0.0,
            'fee_amount' => $this->fee_amount ? (float) $this->fee_amount : 0.0,
            'net_amount' => $this->net_amount ? (float) $this->net_amount : 0.0,
            'paid_amount' => $this->paid_amount ? (float) $this->paid_amount : 0.0,
            'due_amount' => $this->due_amount ? (float) $this->due_amount : 0.0,
            'currency' => $this->currency,
            'exchange_rate' => $this->exchange_rate ? (float) $this->exchange_rate : 0.0,
            'base_currency' => $this->base_currency,
            'base_amount' => $this->base_amount ? (float) $this->base_amount : 0.0,
            'payment_method' => $this->payment_method,
            'payment_method_details' => $this->payment_method_details ? json_decode($this->payment_method_details, true) : [],
            'payment_gateway' => $this->payment_gateway,
            'is_installment' => (bool) $this->is_installment,
            'installment_count' => (int) $this->installment_count,
            'installment_frequency' => $this->installment_frequency,
            'payment_date' => $this->payment_date?->format('M d, Y h:i A'),
            'due_date' => $this->due_date?->format('M d, Y h:i A'),
            'paid_at' => $this->paid_at?->format('M d, Y h:i A'),
            'cancelled_at' => $this->cancelled_at?->format('M d, Y h:i A'),
            'expires_at' => $this->expires_at?->format('M d, Y h:i A'),
            'customer_reference' => $this->customer_reference,
            'bank_reference' => $this->bank_reference,
            'gateway_reference' => $this->gateway_reference,
            'metadata' => $this->metadata ? json_decode($this->metadata, true) : [],
            'custom_fields' => $this->custom_fields ? json_decode($this->custom_fields, true) : [],
            'notes' => $this->notes,
            'failure_reason' => $this->failure_reason,
            'created_at' => $this->created_at?->format('M d, Y h:i A'),
        ];
    }
}

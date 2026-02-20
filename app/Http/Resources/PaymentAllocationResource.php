<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PaymentAllocationResource extends BaseResource
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
            'payment_child_id' => $this->payment_child_id,
            'payment_child_id_details' => $this->PaymentChild,
            'payment_child_name' => $this->PaymentChild?->name ?? $this->PaymentChild?->title ?? $this->PaymentChild?->code ?? null,
            'payment_transaction_id' => $this->payment_transaction_id,
            'payment_transaction_id_details' => $this->PaymentTransaction,
            'payment_transaction_name' => $this->PaymentTransaction?->name ?? $this->PaymentTransaction?->title ?? $this->PaymentTransaction?->code ?? null,
            'allocatable_type' => $this->allocatable_type,
            'allocatable_id' => $this->allocatable_id,
            'amount' => $this->amount ? (float) $this->amount : 0.0,
            'base_amount' => $this->base_amount ? (float) $this->base_amount : 0.0,
            'exchange_rate' => $this->exchange_rate ? (float) $this->exchange_rate : 0.0,
            'currency' => $this->currency,
            'allocation_reference' => $this->allocation_reference,
            'allocation_type' => $this->allocation_type,
            'is_reversed' => (bool) $this->is_reversed,
            'reversed_at' => $this->reversed_at?->format('Y-m-d H:i:s'),
            'reversal_id' => $this->reversal_id,
            'metadata' => $this->metadata ? json_decode($this->metadata, true) : [],
            'notes' => $this->notes,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at?->format('M d, Y h:i A'),
            'updated_at_formatted' => $this->updated_at?->format('M d, Y h:i A'),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'updated_at_human' => $this->updated_at?->diffForHumans(),
        ];
    }
}

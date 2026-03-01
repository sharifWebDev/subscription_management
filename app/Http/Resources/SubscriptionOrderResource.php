<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class SubscriptionOrderResource extends BaseResource
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
            'payment_master_id' => $this->payment_master_id,
            'payment_master_id_details' => $this->PaymentMaster,
            'payment_master_name' => $this->PaymentMaster?->name ?? $this->PaymentMaster?->title ?? $this->PaymentMaster?->code ?? null,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'type' => $this->type,
            'subtotal' => $this->subtotal ? (float) $this->subtotal : 0.0,
            'tax_amount' => $this->tax_amount ? (float) $this->tax_amount : 0.0,
            'discount_amount' => $this->discount_amount ? (float) $this->discount_amount : 0.0,
            'total_amount' => $this->total_amount ? (float) $this->total_amount : 0.0,
            'currency' => $this->currency,
            'customer_info' => $this->customer_info ? json_decode($this->customer_info, true) : [],
            'billing_address' => $this->billing_address ? json_decode($this->billing_address, true) : [],
            'ordered_at' => $this->ordered_at?->format('M d, Y h:i A'),
            'processed_at' => $this->processed_at?->format('M d, Y h:i A'),
            'cancelled_at' => $this->cancelled_at?->format('M d, Y h:i A'),
            'coupon_code' => $this->coupon_code,
            'applied_discounts' => $this->applied_discounts ? json_decode($this->applied_discounts, true) : [],
            'metadata' => $this->metadata ? json_decode($this->metadata, true) : [],
            'notes' => $this->notes,
            'failure_reason' => $this->failure_reason,
            'created_at' => $this->created_at?->format('M d, Y h:i A'),
            'updated_at' => $this->updated_at?->format('M d, Y h:i A'),
        ];
    }
}

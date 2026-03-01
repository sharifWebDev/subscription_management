<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PaymentChildResource extends BaseResource
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
            'subscription_id' => $this->subscription_id,
            'subscription_id_details' => $this->Subscription,
            'subscription_name' => $this->Subscription?->name ?? $this->Subscription?->title ?? $this->Subscription?->code ?? null,
            'plan_id' => $this->plan_id,
            'plan_id_details' => $this->Plan,
            'plan_name' => $this->Plan?->name ?? $this->Plan?->title ?? $this->Plan?->code ?? null,
            'invoice_id' => $this->invoice_id,
            'invoice_id_details' => $this->Invoice,
            'invoice_name' => $this->Invoice?->name ?? $this->Invoice?->title ?? $this->Invoice?->code ?? null,
            'item_type' => $this->item_type,
            'item_id' => $this->item_id,
            'description' => $this->description,
            'item_code' => $this->item_code,
            'unit_price' => $this->unit_price ? (float) $this->unit_price : 0.0,
            'quantity' => (int) $this->quantity,
            'amount' => $this->amount ? (float) $this->amount : 0.0,
            'tax_amount' => $this->tax_amount ? (float) $this->tax_amount : 0.0,
            'discount_amount' => $this->discount_amount ? (float) $this->discount_amount : 0.0,
            'total_amount' => $this->total_amount ? (float) $this->total_amount : 0.0,
            'period_start' => $this->period_start?->format('Y-m-d'),
            'period_end' => $this->period_end?->format('Y-m-d'),
            'billing_cycle' => $this->billing_cycle,
            'status' => $this->status,
            'paid_at' => $this->paid_at?->format('M d, Y h:i A'),
            'allocated_amount' => $this->allocated_amount ? (float) $this->allocated_amount : 0.0,
            'is_fully_allocated' => (bool) $this->is_fully_allocated,
            'metadata' => $this->metadata ? json_decode($this->metadata, true) : [],
            'tax_breakdown' => $this->tax_breakdown ? json_decode($this->tax_breakdown, true) : [],
            'discount_breakdown' => $this->discount_breakdown ? json_decode($this->discount_breakdown, true) : [],
            'created_at' => $this->created_at?->format('M d, Y h:i A'),
        ];
    }
}

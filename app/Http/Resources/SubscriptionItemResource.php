<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class SubscriptionItemResource extends BaseResource
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
            'subscription_id' => $this->subscription_id,
            'subscription_id_details' => $this->Subscription,
            'subscription_name' => $this->Subscription?->name ?? $this->Subscription?->title ?? $this->Subscription?->code ?? null,
            'plan_price_id' => $this->plan_price_id,
            'plan_price_id_details' => $this->PlanPrice,
            'plan_price_name' => $this->PlanPrice?->name ?? $this->PlanPrice?->title ?? $this->PlanPrice?->code ?? null,
            'feature_id' => $this->feature_id,
            'feature_id_details' => $this->Feature,
            'feature_name' => $this->Feature?->name ?? $this->Feature?->title ?? $this->Feature?->code ?? null,
            'quantity' => (int) $this->quantity,
            'unit_price' => $this->unit_price ? (float) $this->unit_price : 0.0,
            'amount' => $this->amount ? (float) $this->amount : 0.0,
            'metadata' => $this->metadata ? json_decode($this->metadata, true) : [],
            'tiers' => $this->tiers ? json_decode($this->tiers, true) : [],
            'effective_from' => $this->effective_from?->format('M d, Y h:i A'),
            'effective_to' => $this->effective_to?->format('M d, Y h:i A'),
            'created_at' => $this->created_at?->format('M d, Y h:i A'),
            'updated_at' => $this->updated_at?->format('M d, Y h:i A')
        ];
    }
}

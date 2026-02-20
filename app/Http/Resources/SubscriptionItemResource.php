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
            'effective_from' => $this->effective_from?->format('Y-m-d H:i:s'),
            'effective_to' => $this->effective_to?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at?->format('M d, Y h:i A'),
            'updated_at_formatted' => $this->updated_at?->format('M d, Y h:i A'),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'updated_at_human' => $this->updated_at?->diffForHumans(),
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UsageRecordResource extends BaseResource
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
            'subscription_id_details' => $this->subscription,
            'subscription_name' => $this->subscription?->plan?->name ?? null,
            'subscription_item_id' => $this->subscription_item_id,
            'subscription_item_id_details' => $this->subscriptionItem,
            'subscription_item_name' => $this->subscriptionItem?->subscription?->plan?->name ?? null,
            'feature_id' => $this->feature_id,
            'feature_id_details' => $this->feature,
            'feature_name' => $this->feature?->name ?? null,
            'quantity' => $this->quantity ? (float) $this->quantity : 0.0,
            'tier_quantity' => $this->tier_quantity ? (float) $this->tier_quantity : 0.0,
            'amount' => $this->amount ? (float) $this->amount : 0.0,
            'unit' => $this->unit,
            'status' => $this->status,
            'recorded_at' => $this->recorded_at?->format('M d, Y h:i A'),
            'billing_date' => $this->billing_date?->format('Y-m-d'),
            'metadata' => $this->metadata ? json_decode($this->metadata, true) : [],
            'dimensions' => $this->dimensions ? json_decode($this->dimensions, true) : [],
            'created_at' => $this->created_at?->format('M d, Y h:i A'),
            'updated_at' => $this->updated_at?->format('M d, Y h:i A'),
        ];
    }
}

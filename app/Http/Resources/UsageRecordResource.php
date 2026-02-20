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
            'subscription_id_details' => $this->Subscription,
            'subscription_name' => $this->Subscription?->name ?? $this->Subscription?->title ?? $this->Subscription?->code ?? null,
            'subscription_item_id' => $this->subscription_item_id,
            'subscription_item_id_details' => $this->SubscriptionItem,
            'subscription_item_name' => $this->SubscriptionItem?->name ?? $this->SubscriptionItem?->title ?? $this->SubscriptionItem?->code ?? null,
            'feature_id' => $this->feature_id,
            'feature_id_details' => $this->Feature,
            'feature_name' => $this->Feature?->name ?? $this->Feature?->title ?? $this->Feature?->code ?? null,
            'quantity' => $this->quantity ? (float) $this->quantity : 0.0,
            'tier_quantity' => $this->tier_quantity ? (float) $this->tier_quantity : 0.0,
            'amount' => $this->amount ? (float) $this->amount : 0.0,
            'unit' => $this->unit,
            'status' => $this->status,
            'recorded_at' => $this->recorded_at?->format('Y-m-d H:i:s'),
            'billing_date' => $this->billing_date?->format('Y-m-d'),
            'metadata' => $this->metadata ? json_decode($this->metadata, true) : [],
            'dimensions' => $this->dimensions ? json_decode($this->dimensions, true) : [],
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at?->format('M d, Y h:i A'),
            'updated_at_formatted' => $this->updated_at?->format('M d, Y h:i A'),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'updated_at_human' => $this->updated_at?->diffForHumans(),
        ];
    }
}

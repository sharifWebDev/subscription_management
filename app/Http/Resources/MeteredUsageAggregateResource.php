<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class MeteredUsageAggregateResource extends BaseResource
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
            'feature_name' => $this->feature?->name ?? $this->feature?->title ?? $this->feature?->code ?? null,
            'aggregate_date' => $this->aggregate_date?->format('Y-m-d'),
            'aggregate_period' => $this->aggregate_period,
            'total_quantity' => $this->total_quantity ? (float) $this->total_quantity : 0.0,
            'tier1_quantity' => $this->tier1_quantity ? (float) $this->tier1_quantity : 0.0,
            'tier2_quantity' => $this->tier2_quantity ? (float) $this->tier2_quantity : 0.0,
            'tier3_quantity' => $this->tier3_quantity ? (float) $this->tier3_quantity : 0.0,
            'total_amount' => $this->total_amount ? (float) $this->total_amount : 0.0,
            'record_count' => (int) $this->record_count,
            'last_calculated_at' => $this->last_calculated_at?->format('M d, Y h:i A'),
            'created_at' => $this->created_at?->format('M d, Y h:i A'),
            'updated_at' => $this->updated_at?->format('M d, Y h:i A'),
        ];
    }
}

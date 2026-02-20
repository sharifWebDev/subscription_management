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
            'subscription_id_details' => $this->Subscription,
            'subscription_name' => $this->Subscription?->name ?? $this->Subscription?->title ?? $this->Subscription?->code ?? null,
            'feature_id' => $this->feature_id,
            'feature_id_details' => $this->Feature,
            'feature_name' => $this->Feature?->name ?? $this->Feature?->title ?? $this->Feature?->code ?? null,
            'aggregate_date' => $this->aggregate_date?->format('Y-m-d'),
            'aggregate_period' => $this->aggregate_period,
            'total_quantity' => $this->total_quantity ? (float) $this->total_quantity : 0.0,
            'tier1_quantity' => $this->tier1_quantity ? (float) $this->tier1_quantity : 0.0,
            'tier2_quantity' => $this->tier2_quantity ? (float) $this->tier2_quantity : 0.0,
            'tier3_quantity' => $this->tier3_quantity ? (float) $this->tier3_quantity : 0.0,
            'total_amount' => $this->total_amount ? (float) $this->total_amount : 0.0,
            'record_count' => (int) $this->record_count,
            'last_calculated_at' => $this->last_calculated_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at?->format('M d, Y h:i A'),
            'updated_at_formatted' => $this->updated_at?->format('M d, Y h:i A'),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'updated_at_human' => $this->updated_at?->diffForHumans(),
        ];
    }
}

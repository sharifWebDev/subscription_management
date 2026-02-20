<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PlanPriceResource extends BaseResource
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
            'plan_id' => $this->plan_id,
            'plan_id_details' => $this->Plan,
            'plan_name' => $this->Plan?->name ?? $this->Plan?->title ?? $this->Plan?->code ?? null,
            'currency' => $this->currency,
            'amount' => $this->amount ? (float) $this->amount : 0.0,
            'interval' => $this->interval,
            'interval_count' => (int) $this->interval_count,
            'usage_type' => $this->usage_type,
            'tiers' => $this->tiers ? json_decode($this->tiers, true) : [],
            'transformations' => $this->transformations ? json_decode($this->transformations, true) : [],
            'stripe_price_id' => $this->stripe_price_id,
            'active_from' => $this->active_from?->format('Y-m-d H:i:s'),
            'active_to' => $this->active_to?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at?->format('M d, Y h:i A'),
            'updated_at_formatted' => $this->updated_at?->format('M d, Y h:i A'),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'updated_at_human' => $this->updated_at?->diffForHumans(),
        ];
    }
}

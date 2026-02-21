<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanPriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'plan_id' => $this->plan_id,
            'currency' => $this->currency,
            'amount' => (float) $this->amount,
            'formatted_amount' => $this->getFormattedAmount(),
            'amount_with_currency' => $this->getAmountWithCurrency(),
            'interval' => $this->interval,
            'interval_count' => $this->interval_count,
            'interval_description' => $this->getIntervalDescription(),
            'usage_type' => $this->usage_type,
            'tiers' => $this->tiers,
            'transformations' => $this->transformations,
            'stripe_price_id' => $this->stripe_price_id,
            'active_from' => $this->active_from?->toISOString(),
            'active_to' => $this->active_to?->toISOString(),
            'is_active' => $this->active_from <= now() &&
                ($this->active_to === null || $this->active_to > now()),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

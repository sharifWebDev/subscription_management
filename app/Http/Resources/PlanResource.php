<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'code' => $this->code,
            'description' => $this->description,
            'type' => $this->type,
            'billing_period' => $this->billing_period,
            'billing_interval' => $this->billing_interval,
            'is_active' => (bool) $this->is_active,
            'is_visible' => (bool) $this->is_visible,
            'sort_order' => $this->sort_order,
            'is_featured' => (bool) $this->is_featured,
            'metadata' => $this->metadata,
            'features' => PlanFeatureResource::collection(
                $this->whenLoaded('planFeatures', function () {
                    return $this->planFeatures->whereNull('effective_to');
                })
            ),
            'prices' => PlanPriceResource::collection(
                $this->whenLoaded('prices', function () {
                    return $this->prices->whereNull('active_to');
                })
            ),
            'discounts' => DiscountResource::collection(
                $this->whenLoaded('discounts')
            ),
            'created_at' => $this->created_at?->toDateString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),
        ];
    }
}

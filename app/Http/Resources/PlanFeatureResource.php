<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanFeatureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'plan_id' => $this->plan_id,
            'feature' => new FeatureResource($this->whenLoaded('feature')),
            'feature_id' => $this->feature_id,
            'feature_code' => $this->feature?->code,
            'feature_name' => $this->feature?->name,
            'value' => $this->value,
            'config' => $this->config,
            'sort_order' => $this->sort_order,
            'is_inherited' => (bool) $this->is_inherited,
            'effective_from' => $this->effective_from?->toISOString(),
            'effective_to' => $this->effective_to?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

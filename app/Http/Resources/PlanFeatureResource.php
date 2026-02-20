<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PlanFeatureResource extends BaseResource
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
            'feature_id' => $this->feature_id,
            'feature_id_details' => $this->Feature,
            'feature_name' => $this->Feature?->name ?? $this->Feature?->title ?? $this->Feature?->code ?? null,
            'value' => $this->value,
            'config' => $this->config ? json_decode($this->config, true) : [],
            'sort_order' => (int) $this->sort_order,
            'is_inherited' => (bool) $this->is_inherited,
            'parent_feature_id' => $this->parent_feature_id,
            'effective_from' => $this->effective_from?->format('Y-m-d H:i:s'),
            'effective_to' => $this->effective_to?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at?->format('M d, Y h:i A'),
        ];
    }
}

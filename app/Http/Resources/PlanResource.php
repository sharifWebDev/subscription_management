<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PlanResource extends BaseResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'code' => $this->code,
            'description' => $this->description,
            'type' => $this->type,
            'billing_period' => $this->billing_period,
            'billing_interval' => (int) $this->billing_interval,
            'is_active' => (bool) $this->is_active,
            'is_visible' => (bool) $this->is_visible,
            'sort_order' => (int) $this->sort_order,
            'is_featured' => (bool) $this->is_featured,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at?->format('M d, Y h:i A'),
            'updated_at_formatted' => $this->updated_at?->format('M d, Y h:i A'),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'updated_at_human' => $this->updated_at?->diffForHumans(),
        ];
    }
}

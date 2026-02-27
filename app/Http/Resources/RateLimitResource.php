<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class RateLimitResource extends BaseResource
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
            'feature_id' => $this->feature_id,
            'feature_id_details' => $this->feature,
            'feature_name' => $this->feature?->name ?? $this->feature?->title ?? $this->feature?->code ?? null,
            'key' => $this->key,
            'max_attempts' => (int) $this->max_attempts,
            'decay_seconds' => (int) $this->decay_seconds,
            'remaining' => (int) $this->remaining,
            'resets_at' => $this->resets_at?->format('M d, Y h:i A'),
            'created_at' => $this->created_at?->format('M d, Y h:i A'),
            'updated_at' => $this->updated_at?->format('M d, Y h:i A')
        ];
    }
}

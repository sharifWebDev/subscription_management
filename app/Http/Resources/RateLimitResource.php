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
            'subscription_id_details' => $this->Subscription,
            'subscription_name' => $this->Subscription?->name ?? $this->Subscription?->title ?? $this->Subscription?->code ?? null,
            'feature_id' => $this->feature_id,
            'feature_id_details' => $this->Feature,
            'feature_name' => $this->Feature?->name ?? $this->Feature?->title ?? $this->Feature?->code ?? null,
            'key' => $this->key,
            'max_attempts' => (int) $this->max_attempts,
            'decay_seconds' => (int) $this->decay_seconds,
            'remaining' => (int) $this->remaining,
            'resets_at' => $this->resets_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at?->format('M d, Y h:i A'),
            'updated_at_formatted' => $this->updated_at?->format('M d, Y h:i A'),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'updated_at_human' => $this->updated_at?->diffForHumans(),
        ];
    }
}

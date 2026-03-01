<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class SubscriptionEventResource extends BaseResource
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
            'type' => $this->type,
            'subscription_name' => $this->subscription?->plan?->name ?? null,
            'data' => $this->data
                ? collect(json_decode($this->data, true))
                    ->map(fn ($value, $key) => "$key: $value")
                    ->implode(', ')
                : '',
            'changes' => $this->changes = is_string($this->changes) && str_contains($this->changes, ',') && explode(', ', $this->changes)
                ? collect(explode(', ', $this->changes))
                    ->map(fn ($value) => trim($value))
                    ->implode(', ')
                : '',
            'causer_id' => $this->causer_id,
            'causer' => $this->causerUser?->name,
            'causer_type' => $this->causer_type,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'metadata' => $this->metadata ? json_decode($this->metadata, true) : [],
            'occurred_at' => $this->occurred_at?->format('M d, Y h:i A'),
            'created_at' => $this->created_at?->format('M d, Y h:i A'),
            'updated_at' => $this->updated_at?->format('M d, Y h:i A'),
        ];
    }
}

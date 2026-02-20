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
            'subscription_id_details' => $this->Subscription,
            'subscription_name' => $this->Subscription?->name ?? $this->Subscription?->title ?? $this->Subscription?->code ?? null,
            'type' => $this->type,
            'data' => $this->data ? json_decode($this->data, true) : [],
            'changes' => $this->changes ? json_decode($this->changes, true) : [],
            'causer_id' => $this->causer_id,
            'causer_type' => $this->causer_type,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'metadata' => $this->metadata ? json_decode($this->metadata, true) : [],
            'occurred_at' => $this->occurred_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at?->format('M d, Y h:i A'),
            'updated_at_formatted' => $this->updated_at?->format('M d, Y h:i A'),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'updated_at_human' => $this->updated_at?->diffForHumans(),
        ];
    }
}

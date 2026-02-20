<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class DiscountResource extends BaseResource
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
            'code' => $this->code,
            'name' => $this->name,
            'type' => $this->type,
            'amount' => $this->amount ? (float) $this->amount : 0.0,
            'currency' => $this->currency,
            'applies_to' => $this->applies_to,
            'applies_to_ids' => $this->applies_to_ids,
            'max_redemptions' => (int) $this->max_redemptions,
            'times_redeemed' => (int) $this->times_redeemed,
            'is_active' => (bool) $this->is_active,
            'starts_at' => $this->starts_at?->format('Y-m-d H:i:s'),
            'expires_at' => $this->expires_at?->format('Y-m-d H:i:s'),
            'duration' => $this->duration,
            'duration_in_months' => (int) $this->duration_in_months,
            'metadata' => $this->metadata ? json_decode($this->metadata, true) : [],
            'restrictions' => $this->restrictions ? json_decode($this->restrictions, true) : [],
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at?->format('M d, Y h:i A'),
            'created_at_human' => $this->created_at?->diffForHumans(),
        ];
    }
}

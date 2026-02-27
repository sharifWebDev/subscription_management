<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class FeatureResource extends BaseResource
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
            'code' => $this->code,
            'description' => $this->description,
            'type' => $this->type,
            'scope' => $this->scope,
            'is_resettable' => (bool) $this->is_resettable,
            'reset_period' => $this->reset_period,
            'metadata' => $this->metadata ? json_decode($this->metadata, true) : [],
            'validations' => $this->validations ? json_decode($this->validations, true) : [],
            'created_at' => $this->created_at?->format('M d, Y h:i A')
        ];
    }
}

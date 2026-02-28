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
            'metadata' => $this->metadata 
                ? collect(json_decode($this->metadata, true))
                    ->map(fn($value, $key) => "$key: $value")
                    ->implode(', ')
                : '',
            'validations' => $this->validations 
                ? collect(json_decode($this->validations, true))
                    ->map(fn($value, $key) => "$key: $value")
                    ->implode(', ')
                : '',
            'created_at' => $this->created_at?->format('M d, Y h:i A')
        ];
    }
}

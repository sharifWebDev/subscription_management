<?php

namespace App\Dtos;

class PlanDto
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?string $slug,
        public readonly string $code,
        public readonly ?string $description,
        public readonly string $type,
        public readonly string $billing_period,
        public readonly int $billing_interval,
        public readonly bool $is_active,
        public readonly bool $is_visible,
        public readonly int $sort_order,
        public readonly bool $is_featured,
        public readonly ?array $metadata,
        public readonly array $features = [],
        public readonly array $prices = [],
        public readonly array $discounts = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'],
            slug: $data['slug'] ?? null,
            code: $data['code'] ?? strtoupper(\Str::slug($data['name'], '_')),
            description: $data['description'] ?? null,
            type: $data['type'] ?? 'recurring',
            billing_period: $data['billing_period'] ?? 'monthly',
            billing_interval: $data['billing_interval'] ?? 1,
            is_active: $data['is_active'] ?? true,
            is_visible: $data['is_visible'] ?? true,
            sort_order: $data['sort_order'] ?? 0,
            is_featured: $data['is_featured'] ?? false,
            metadata: $data['metadata'] ?? null,
            features: $data['features'] ?? [],
            prices: $data['prices'] ?? [],
            discounts: $data['discounts'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'code' => $this->code,
            'description' => $this->description,
            'type' => $this->type,
            'billing_period' => $this->billing_period,
            'billing_interval' => $this->billing_interval,
            'is_active' => $this->is_active,
            'is_visible' => $this->is_visible,
            'sort_order' => $this->sort_order,
            'is_featured' => $this->is_featured,
            'metadata' => $this->metadata,
            'features' => $this->features,
            'prices' => $this->prices,
            'discounts' => $this->discounts,
        ];
    }
}

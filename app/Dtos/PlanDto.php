<?php

namespace App\Dtos;

class PlansDto implements \JsonSerializable
{
    private mixed $id;

    private mixed $name;

    private mixed $slug;

    private mixed $code;

    private ?string $description;

    private mixed $type;

    private mixed $billing_period;

    private int $billing_interval;

    private bool $is_active;

    private bool $is_visible;

    private int $sort_order;

    private bool $is_featured;

    private ?array $metadata;

    private ?mixed $created_by;

    private ?mixed $updated_by;

    private string $created_at;

    private string $updated_at;

    private ?string $deleted_at;

    public function __construct(array $data = [])
    {
        $this->setId($data['id'] ?? null);
        $this->setName($data['name'] ?? null);
        $this->setSlug($data['slug'] ?? null);
        $this->setCode($data['code'] ?? null);
        $this->setDescription($data['description'] ?? null);
        $this->setType($data['type'] ?? null);
        $this->setBillingPeriod($data['billing_period'] ?? null);
        $this->setBillingInterval($data['billing_interval'] ?? 1);
        $this->setIsActive($data['is_active'] ?? true);
        $this->setIsVisible($data['is_visible'] ?? true);
        $this->setSortOrder($data['sort_order'] ?? 0);
        $this->setIsFeatured($data['is_featured'] ?? false);
        $this->setMetadata($data['metadata'] ?? null);
        $this->setCreatedBy($data['created_by'] ?? null);
        $this->setUpdatedBy($data['updated_by'] ?? null);
        $this->setCreatedAt($data['created_at'] ?? null);
        $this->setUpdatedAt($data['updated_at'] ?? null);
        $this->setDeletedAt($data['deleted_at'] ?? null);
    }

    public function setId(mixed $value): void
    {
        $this->id = $value;
    }

    public function getId(): mixed
    {
        return $this->id;
    }

    public function setName(mixed $value): void
    {
        $this->name = $value;
    }

    public function getName(): mixed
    {
        return $this->name;
    }

    public function setSlug(mixed $value): void
    {
        $this->slug = $value;
    }

    public function getSlug(): mixed
    {
        return $this->slug;
    }

    public function setCode(mixed $value): void
    {
        $this->code = $value;
    }

    public function getCode(): mixed
    {
        return $this->code;
    }

    public function setDescription(?string $value): void
    {
        $this->description = $value;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setType(mixed $value): void
    {
        $this->type = $value;
    }

    public function getType(): mixed
    {
        return $this->type;
    }

    public function setBillingPeriod(mixed $value): void
    {
        $this->billing_period = $value;
    }

    public function getBillingPeriod(): mixed
    {
        return $this->billing_period;
    }

    public function setBillingInterval(int $value): void
    {
        $this->billing_interval = $value;
    }

    public function getBillingInterval(): int
    {
        return $this->billing_interval;
    }

    public function setIsActive(bool $value): void
    {
        $this->is_active = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    public function getIsActive(): bool
    {
        return $this->is_active ?? false;
    }

    public function setIsVisible(bool $value): void
    {
        $this->is_visible = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    public function getIsVisible(): bool
    {
        return $this->is_visible ?? false;
    }

    public function setSortOrder(int $value): void
    {
        $this->sort_order = $value;
    }

    public function getSortOrder(): int
    {
        return $this->sort_order;
    }

    public function setIsFeatured(bool $value): void
    {
        $this->is_featured = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    public function getIsFeatured(): bool
    {
        return $this->is_featured ?? false;
    }

    public function setMetadata(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->metadata = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->metadata = $value;
        }
    }

    public function getMetadata(): array
    {
        if (is_array($this->metadata)) {
            return $this->metadata;
        }

        if (is_string($this->metadata)) {
            $decoded = json_decode($this->metadata, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
    }

    public function setCreatedBy(?mixed $value): void
    {
        $this->created_by = $value;
    }

    public function getCreatedBy(): ?mixed
    {
        return $this->created_by;
    }

    public function setUpdatedBy(?mixed $value): void
    {
        $this->updated_by = $value;
    }

    public function getUpdatedBy(): ?mixed
    {
        return $this->updated_by;
    }

    public function setCreatedAt(string $value): void
    {
        $this->created_at = $value;
    }

    public function getCreatedAt(): ?string
    {
        if ($this->created_at instanceof \DateTimeInterface) {
            return $this->created_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->created_at)) {
            $timestamp = strtotime($this->created_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setUpdatedAt(string $value): void
    {
        $this->updated_at = $value;
    }

    public function getUpdatedAt(): ?string
    {
        if ($this->updated_at instanceof \DateTimeInterface) {
            return $this->updated_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->updated_at)) {
            $timestamp = strtotime($this->updated_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setDeletedAt(?string $value): void
    {
        $this->deleted_at = $value;
    }

    public function getDeletedAt(): ?string
    {
        if ($this->deleted_at instanceof \DateTimeInterface) {
            return $this->deleted_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->deleted_at)) {
            $timestamp = strtotime($this->deleted_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'slug' => $this->getSlug(),
            'code' => $this->getCode(),
            'description' => $this->getDescription(),
            'type' => $this->getType(),
            'billing_period' => $this->getBillingPeriod(),
            'billing_interval' => $this->getBillingInterval(),
            'is_active' => $this->getIsActive(),
            'is_visible' => $this->getIsVisible(),
            'sort_order' => $this->getSortOrder(),
            'is_featured' => $this->getIsFeatured(),
            'metadata' => $this->getMetadata(),
            'created_by' => $this->getCreatedBy(),
            'updated_by' => $this->getUpdatedBy(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
            'deleted_at' => $this->getDeletedAt(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function __toString(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }
}

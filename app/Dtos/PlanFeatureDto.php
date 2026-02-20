<?php

namespace App\Dtos;

class PlanFeaturesDto implements \JsonSerializable
{
    private mixed $id;

    private mixed $plan_id;

    private mixed $feature_id;

    private mixed $value;

    private ?array $config;

    private int $sort_order;

    private bool $is_inherited;

    private ?mixed $parent_feature_id;

    private string $effective_from;

    private ?string $effective_to;

    private ?mixed $created_by;

    private ?mixed $updated_by;

    private string $created_at;

    private string $updated_at;

    private ?string $deleted_at;

    public function __construct(array $data = [])
    {
        $this->setId($data['id'] ?? null);
        $this->setPlanId($data['plan_id'] ?? null);
        $this->setFeatureId($data['feature_id'] ?? null);
        $this->setValue($data['value'] ?? null);
        $this->setConfig($data['config'] ?? null);
        $this->setSortOrder($data['sort_order'] ?? 0);
        $this->setIsInherited($data['is_inherited'] ?? false);
        $this->setParentFeatureId($data['parent_feature_id'] ?? null);
        $this->setEffectiveFrom($data['effective_from'] ?? null);
        $this->setEffectiveTo($data['effective_to'] ?? null);
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

    public function setPlanId(mixed $value): void
    {
        $this->plan_id = $value;
    }

    public function getPlanId(): mixed
    {
        return $this->plan_id;
    }

    public function setFeatureId(mixed $value): void
    {
        $this->feature_id = $value;
    }

    public function getFeatureId(): mixed
    {
        return $this->feature_id;
    }

    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setConfig(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->config = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->config = $value;
        }
    }

    public function getConfig(): array
    {
        if (is_array($this->config)) {
            return $this->config;
        }

        if (is_string($this->config)) {
            $decoded = json_decode($this->config, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
    }

    public function setSortOrder(int $value): void
    {
        $this->sort_order = $value;
    }

    public function getSortOrder(): int
    {
        return $this->sort_order;
    }

    public function setIsInherited(bool $value): void
    {
        $this->is_inherited = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    public function getIsInherited(): bool
    {
        return $this->is_inherited ?? false;
    }

    public function setParentFeatureId(?mixed $value): void
    {
        $this->parent_feature_id = $value;
    }

    public function getParentFeatureId(): ?mixed
    {
        return $this->parent_feature_id;
    }

    public function setEffectiveFrom(string $value): void
    {
        $this->effective_from = $value;
    }

    public function getEffectiveFrom(): ?string
    {
        if ($this->effective_from instanceof \DateTimeInterface) {
            return $this->effective_from->format('Y-m-d H:i:s');
        }

        if (is_string($this->effective_from)) {
            $timestamp = strtotime($this->effective_from);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setEffectiveTo(?string $value): void
    {
        $this->effective_to = $value;
    }

    public function getEffectiveTo(): ?string
    {
        if ($this->effective_to instanceof \DateTimeInterface) {
            return $this->effective_to->format('Y-m-d H:i:s');
        }

        if (is_string($this->effective_to)) {
            $timestamp = strtotime($this->effective_to);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
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
            'plan_id' => $this->getPlanId(),
            'feature_id' => $this->getFeatureId(),
            'value' => $this->getValue(),
            'config' => $this->getConfig(),
            'sort_order' => $this->getSortOrder(),
            'is_inherited' => $this->getIsInherited(),
            'parent_feature_id' => $this->getParentFeatureId(),
            'effective_from' => $this->getEffectiveFrom(),
            'effective_to' => $this->getEffectiveTo(),
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

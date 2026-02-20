<?php

namespace App\Dtos;

class SubscriptionItemsDto implements \JsonSerializable
{
    private mixed $id;

    private mixed $subscription_id;

    private mixed $plan_price_id;

    private mixed $feature_id;

    private int $quantity;

    private float $unit_price;

    private float $amount;

    private ?array $metadata;

    private ?array $tiers;

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
        $this->setSubscriptionId($data['subscription_id'] ?? null);
        $this->setPlanPriceId($data['plan_price_id'] ?? null);
        $this->setFeatureId($data['feature_id'] ?? null);
        $this->setQuantity($data['quantity'] ?? 1);
        $this->setUnitPrice($data['unit_price'] ?? 0);
        $this->setAmount($data['amount'] ?? 0);
        $this->setMetadata($data['metadata'] ?? null);
        $this->setTiers($data['tiers'] ?? null);
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

    public function setSubscriptionId(mixed $value): void
    {
        $this->subscription_id = $value;
    }

    public function getSubscriptionId(): mixed
    {
        return $this->subscription_id;
    }

    public function setPlanPriceId(mixed $value): void
    {
        $this->plan_price_id = $value;
    }

    public function getPlanPriceId(): mixed
    {
        return $this->plan_price_id;
    }

    public function setFeatureId(mixed $value): void
    {
        $this->feature_id = $value;
    }

    public function getFeatureId(): mixed
    {
        return $this->feature_id;
    }

    public function setQuantity(int $value): void
    {
        $this->quantity = $value;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setUnitPrice(float $value): void
    {
        $this->unit_price = $value;
    }

    public function getUnitPrice(): float
    {
        return $this->unit_price;
    }

    public function setAmount(float $value): void
    {
        $this->amount = $value;
    }

    public function getAmount(): float
    {
        return $this->amount;
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

    public function setTiers(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->tiers = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->tiers = $value;
        }
    }

    public function getTiers(): array
    {
        if (is_array($this->tiers)) {
            return $this->tiers;
        }

        if (is_string($this->tiers)) {
            $decoded = json_decode($this->tiers, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
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
            'subscription_id' => $this->getSubscriptionId(),
            'plan_price_id' => $this->getPlanPriceId(),
            'feature_id' => $this->getFeatureId(),
            'quantity' => $this->getQuantity(),
            'unit_price' => $this->getUnitPrice(),
            'amount' => $this->getAmount(),
            'metadata' => $this->getMetadata(),
            'tiers' => $this->getTiers(),
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

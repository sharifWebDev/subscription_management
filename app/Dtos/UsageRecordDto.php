<?php

namespace App\Dtos;

class UsageRecordsDto implements \JsonSerializable
{
    private mixed $id;

    private mixed $subscription_id;

    private mixed $subscription_item_id;

    private mixed $feature_id;

    private float $quantity;

    private ?float $tier_quantity;

    private ?float $amount;

    private mixed $unit;

    private mixed $status;

    private string $recorded_at;

    private string $billing_date;

    private ?array $metadata;

    private ?array $dimensions;

    private ?mixed $created_by;

    private ?mixed $updated_by;

    private string $created_at;

    private string $updated_at;

    private ?string $deleted_at;

    public function __construct(array $data = [])
    {
        $this->setId($data['id'] ?? null);
        $this->setSubscriptionId($data['subscription_id'] ?? null);
        $this->setSubscriptionItemId($data['subscription_item_id'] ?? null);
        $this->setFeatureId($data['feature_id'] ?? null);
        $this->setQuantity($data['quantity'] ?? 0);
        $this->setTierQuantity($data['tier_quantity'] ?? null);
        $this->setAmount($data['amount'] ?? null);
        $this->setUnit($data['unit'] ?? null);
        $this->setStatus($data['status'] ?? null);
        $this->setRecordedAt($data['recorded_at'] ?? null);
        $this->setBillingDate($data['billing_date'] ?? '');
        $this->setMetadata($data['metadata'] ?? null);
        $this->setDimensions($data['dimensions'] ?? null);
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

    public function setSubscriptionItemId(mixed $value): void
    {
        $this->subscription_item_id = $value;
    }

    public function getSubscriptionItemId(): mixed
    {
        return $this->subscription_item_id;
    }

    public function setFeatureId(mixed $value): void
    {
        $this->feature_id = $value;
    }

    public function getFeatureId(): mixed
    {
        return $this->feature_id;
    }

    public function setQuantity(float $value): void
    {
        $this->quantity = $value;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function setTierQuantity(?float $value): void
    {
        $this->tier_quantity = $value;
    }

    public function getTierQuantity(): ?float
    {
        return $this->tier_quantity;
    }

    public function setAmount(?float $value): void
    {
        $this->amount = $value;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setUnit(mixed $value): void
    {
        $this->unit = $value;
    }

    public function getUnit(): mixed
    {
        return $this->unit;
    }

    public function setStatus(mixed $value): void
    {
        $this->status = $value;
    }

    public function getStatus(): mixed
    {
        return $this->status;
    }

    public function setRecordedAt(string $value): void
    {
        $this->recorded_at = $value;
    }

    public function getRecordedAt(): ?string
    {
        if ($this->recorded_at instanceof \DateTimeInterface) {
            return $this->recorded_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->recorded_at)) {
            $timestamp = strtotime($this->recorded_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setBillingDate(string $value): void
    {
        $this->billing_date = $value;
    }

    public function getBillingDate(): string
    {
        return $this->billing_date;
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

    public function setDimensions(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->dimensions = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->dimensions = $value;
        }
    }

    public function getDimensions(): array
    {
        if (is_array($this->dimensions)) {
            return $this->dimensions;
        }

        if (is_string($this->dimensions)) {
            $decoded = json_decode($this->dimensions, true);

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
            'subscription_id' => $this->getSubscriptionId(),
            'subscription_item_id' => $this->getSubscriptionItemId(),
            'feature_id' => $this->getFeatureId(),
            'quantity' => $this->getQuantity(),
            'tier_quantity' => $this->getTierQuantity(),
            'amount' => $this->getAmount(),
            'unit' => $this->getUnit(),
            'status' => $this->getStatus(),
            'recorded_at' => $this->getRecordedAt(),
            'billing_date' => $this->getBillingDate(),
            'metadata' => $this->getMetadata(),
            'dimensions' => $this->getDimensions(),
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

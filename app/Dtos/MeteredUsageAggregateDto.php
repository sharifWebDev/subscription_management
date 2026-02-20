<?php

namespace App\Dtos;

class MeteredUsageAggregatesDto implements \JsonSerializable
{
    private mixed $id;

    private mixed $subscription_id;

    private mixed $feature_id;

    private string $aggregate_date;

    private mixed $aggregate_period;

    private float $total_quantity;

    private float $tier1_quantity;

    private float $tier2_quantity;

    private float $tier3_quantity;

    private float $total_amount;

    private int $record_count;

    private string $last_calculated_at;

    private ?mixed $created_by;

    private ?mixed $updated_by;

    private string $created_at;

    private string $updated_at;

    private ?string $deleted_at;

    public function __construct(array $data = [])
    {
        $this->setId($data['id'] ?? null);
        $this->setSubscriptionId($data['subscription_id'] ?? null);
        $this->setFeatureId($data['feature_id'] ?? null);
        $this->setAggregateDate($data['aggregate_date'] ?? '');
        $this->setAggregatePeriod($data['aggregate_period'] ?? null);
        $this->setTotalQuantity($data['total_quantity'] ?? 0);
        $this->setTier1Quantity($data['tier1_quantity'] ?? 0.00000000);
        $this->setTier2Quantity($data['tier2_quantity'] ?? 0.00000000);
        $this->setTier3Quantity($data['tier3_quantity'] ?? 0.00000000);
        $this->setTotalAmount($data['total_amount'] ?? 0.00000000);
        $this->setRecordCount($data['record_count'] ?? 0);
        $this->setLastCalculatedAt($data['last_calculated_at'] ?? null);
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

    public function setFeatureId(mixed $value): void
    {
        $this->feature_id = $value;
    }

    public function getFeatureId(): mixed
    {
        return $this->feature_id;
    }

    public function setAggregateDate(string $value): void
    {
        $this->aggregate_date = $value;
    }

    public function getAggregateDate(): string
    {
        return $this->aggregate_date;
    }

    public function setAggregatePeriod(mixed $value): void
    {
        $this->aggregate_period = $value;
    }

    public function getAggregatePeriod(): mixed
    {
        return $this->aggregate_period;
    }

    public function setTotalQuantity(float $value): void
    {
        $this->total_quantity = $value;
    }

    public function getTotalQuantity(): float
    {
        return $this->total_quantity;
    }

    public function setTier1Quantity(float $value): void
    {
        $this->tier1_quantity = $value;
    }

    public function getTier1Quantity(): float
    {
        return $this->tier1_quantity;
    }

    public function setTier2Quantity(float $value): void
    {
        $this->tier2_quantity = $value;
    }

    public function getTier2Quantity(): float
    {
        return $this->tier2_quantity;
    }

    public function setTier3Quantity(float $value): void
    {
        $this->tier3_quantity = $value;
    }

    public function getTier3Quantity(): float
    {
        return $this->tier3_quantity;
    }

    public function setTotalAmount(float $value): void
    {
        $this->total_amount = $value;
    }

    public function getTotalAmount(): float
    {
        return $this->total_amount;
    }

    public function setRecordCount(int $value): void
    {
        $this->record_count = $value;
    }

    public function getRecordCount(): int
    {
        return $this->record_count;
    }

    public function setLastCalculatedAt(string $value): void
    {
        $this->last_calculated_at = $value;
    }

    public function getLastCalculatedAt(): ?string
    {
        if ($this->last_calculated_at instanceof \DateTimeInterface) {
            return $this->last_calculated_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->last_calculated_at)) {
            $timestamp = strtotime($this->last_calculated_at);
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
            'feature_id' => $this->getFeatureId(),
            'aggregate_date' => $this->getAggregateDate(),
            'aggregate_period' => $this->getAggregatePeriod(),
            'total_quantity' => $this->getTotalQuantity(),
            'tier1_quantity' => $this->getTier1Quantity(),
            'tier2_quantity' => $this->getTier2Quantity(),
            'tier3_quantity' => $this->getTier3Quantity(),
            'total_amount' => $this->getTotalAmount(),
            'record_count' => $this->getRecordCount(),
            'last_calculated_at' => $this->getLastCalculatedAt(),
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

<?php

namespace App\Dtos;

class PlanPricesDto implements \JsonSerializable
{
    private mixed $id;

    private mixed $plan_id;

    private mixed $currency;

    private float $amount;

    private mixed $interval;

    private int $interval_count;

    private mixed $usage_type;

    private ?array $tiers;

    private ?array $transformations;

    private ?mixed $stripe_price_id;

    private string $active_from;

    private ?string $active_to;

    private ?mixed $created_by;

    private ?mixed $updated_by;

    private string $created_at;

    private string $updated_at;

    private ?string $deleted_at;

    public function __construct(array $data = [])
    {
        $this->setId($data['id'] ?? null);
        $this->setPlanId($data['plan_id'] ?? null);
        $this->setCurrency($data['currency'] ?? null);
        $this->setAmount($data['amount'] ?? 0);
        $this->setInterval($data['interval'] ?? null);
        $this->setIntervalCount($data['interval_count'] ?? 1);
        $this->setUsageType($data['usage_type'] ?? null);
        $this->setTiers($data['tiers'] ?? null);
        $this->setTransformations($data['transformations'] ?? null);
        $this->setStripePriceId($data['stripe_price_id'] ?? null);
        $this->setActiveFrom($data['active_from'] ?? null);
        $this->setActiveTo($data['active_to'] ?? null);
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

    public function setCurrency(mixed $value): void
    {
        $this->currency = $value;
    }

    public function getCurrency(): mixed
    {
        return $this->currency;
    }

    public function setAmount(float $value): void
    {
        $this->amount = $value;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setInterval(mixed $value): void
    {
        $this->interval = $value;
    }

    public function getInterval(): mixed
    {
        return $this->interval;
    }

    public function setIntervalCount(int $value): void
    {
        $this->interval_count = $value;
    }

    public function getIntervalCount(): int
    {
        return $this->interval_count;
    }

    public function setUsageType(mixed $value): void
    {
        $this->usage_type = $value;
    }

    public function getUsageType(): mixed
    {
        return $this->usage_type;
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

    public function setTransformations(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->transformations = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->transformations = $value;
        }
    }

    public function getTransformations(): array
    {
        if (is_array($this->transformations)) {
            return $this->transformations;
        }

        if (is_string($this->transformations)) {
            $decoded = json_decode($this->transformations, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
    }

    public function setStripePriceId(?mixed $value): void
    {
        $this->stripe_price_id = $value;
    }

    public function getStripePriceId(): ?mixed
    {
        return $this->stripe_price_id;
    }

    public function setActiveFrom(string $value): void
    {
        $this->active_from = $value;
    }

    public function getActiveFrom(): ?string
    {
        if ($this->active_from instanceof \DateTimeInterface) {
            return $this->active_from->format('Y-m-d H:i:s');
        }

        if (is_string($this->active_from)) {
            $timestamp = strtotime($this->active_from);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setActiveTo(?string $value): void
    {
        $this->active_to = $value;
    }

    public function getActiveTo(): ?string
    {
        if ($this->active_to instanceof \DateTimeInterface) {
            return $this->active_to->format('Y-m-d H:i:s');
        }

        if (is_string($this->active_to)) {
            $timestamp = strtotime($this->active_to);
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
            'currency' => $this->getCurrency(),
            'amount' => $this->getAmount(),
            'interval' => $this->getInterval(),
            'interval_count' => $this->getIntervalCount(),
            'usage_type' => $this->getUsageType(),
            'tiers' => $this->getTiers(),
            'transformations' => $this->getTransformations(),
            'stripe_price_id' => $this->getStripePriceId(),
            'active_from' => $this->getActiveFrom(),
            'active_to' => $this->getActiveTo(),
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

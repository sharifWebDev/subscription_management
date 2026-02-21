<?php

namespace App\Dtos;

class DiscountsDto implements \JsonSerializable
{
    private mixed $id;

    private mixed $code;

    private mixed $name;

    private mixed $type;

    private float $amount;

    private mixed $currency;

    private mixed $applies_to;

    private ?array $applies_to_ids;

    private ?int $max_redemptions;

    private int $times_redeemed;

    private bool $is_active;

    private ?string $starts_at;

    private ?string $expires_at;

    private mixed $duration;

    private ?int $duration_in_months;

    private ?array $metadata;

    private ?array $restrictions;

    private mixed $created_by;

    private mixed $updated_by;

    private string $created_at;

    private string $updated_at;

    private ?string $deleted_at;

    public function __construct(array $data = [])
    {
        $this->setId($data['id'] ?? null);
        $this->setCode($data['code'] ?? null);
        $this->setName($data['name'] ?? null);
        $this->setType($data['type'] ?? null);
        $this->setAmount($data['amount'] ?? 0);
        $this->setCurrency($data['currency'] ?? null);
        $this->setAppliesTo($data['applies_to'] ?? null);
        $this->setAppliesToIds($data['applies_to_ids'] ?? null);
        $this->setMaxRedemptions($data['max_redemptions'] ?? null);
        $this->setTimesRedeemed($data['times_redeemed'] ?? 0);
        $this->setIsActive($data['is_active'] ?? true);
        $this->setStartsAt($data['starts_at'] ?? null);
        $this->setExpiresAt($data['expires_at'] ?? null);
        $this->setDuration($data['duration'] ?? null);
        $this->setDurationInMonths($data['duration_in_months'] ?? null);
        $this->setMetadata($data['metadata'] ?? null);
        $this->setRestrictions($data['restrictions'] ?? null);
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

    public function setCode(mixed $value): void
    {
        $this->code = $value;
    }

    public function getCode(): mixed
    {
        return $this->code;
    }

    public function setName(mixed $value): void
    {
        $this->name = $value;
    }

    public function getName(): mixed
    {
        return $this->name;
    }

    public function setType(mixed $value): void
    {
        $this->type = $value;
    }

    public function getType(): mixed
    {
        return $this->type;
    }

    public function setAmount(float $value): void
    {
        $this->amount = $value;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setCurrency(mixed $value): void
    {
        $this->currency = $value;
    }

    public function getCurrency(): mixed
    {
        return $this->currency;
    }

    public function setAppliesTo(mixed $value): void
    {
        $this->applies_to = $value;
    }

    public function getAppliesTo(): mixed
    {
        return $this->applies_to;
    }

    public function setAppliesToIds(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->applies_to_ids = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->applies_to_ids = $value;
        }
    }

    public function getAppliesToIds(): array
    {
        if (is_array($this->applies_to_ids)) {
            return $this->applies_to_ids;
        }

        if (is_string($this->applies_to_ids)) {
            $decoded = json_decode($this->applies_to_ids, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
    }

    public function setMaxRedemptions(?int $value): void
    {
        $this->max_redemptions = $value;
    }

    public function getMaxRedemptions(): ?int
    {
        return $this->max_redemptions;
    }

    public function setTimesRedeemed(int $value): void
    {
        $this->times_redeemed = $value;
    }

    public function getTimesRedeemed(): int
    {
        return $this->times_redeemed;
    }

    public function setIsActive(bool $value): void
    {
        $this->is_active = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    public function getIsActive(): bool
    {
        return $this->is_active ?? false;
    }

    public function setStartsAt(?string $value): void
    {
        $this->starts_at = $value;
    }

    public function getStartsAt(): ?string
    {
        if ($this->starts_at instanceof \DateTimeInterface) {
            return $this->starts_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->starts_at)) {
            $timestamp = strtotime($this->starts_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setExpiresAt(?string $value): void
    {
        $this->expires_at = $value;
    }

    public function getExpiresAt(): ?string
    {
        if ($this->expires_at instanceof \DateTimeInterface) {
            return $this->expires_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->expires_at)) {
            $timestamp = strtotime($this->expires_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setDuration(mixed $value): void
    {
        $this->duration = $value;
    }

    public function getDuration(): mixed
    {
        return $this->duration;
    }

    public function setDurationInMonths(?int $value): void
    {
        $this->duration_in_months = $value;
    }

    public function getDurationInMonths(): ?int
    {
        return $this->duration_in_months;
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

    public function setRestrictions(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->restrictions = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->restrictions = $value;
        }
    }

    public function getRestrictions(): array
    {
        if (is_array($this->restrictions)) {
            return $this->restrictions;
        }

        if (is_string($this->restrictions)) {
            $decoded = json_decode($this->restrictions, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
    }

    public function setCreatedBy(mixed $value): void
    {
        $this->created_by = $value;
    }

    public function getCreatedBy(): mixed
    {
        return $this->created_by;
    }

    public function setUpdatedBy(mixed $value): void
    {
        $this->updated_by = $value;
    }

    public function getUpdatedBy(): mixed
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
            'code' => $this->getCode(),
            'name' => $this->getName(),
            'type' => $this->getType(),
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'applies_to' => $this->getAppliesTo(),
            'applies_to_ids' => $this->getAppliesToIds(),
            'max_redemptions' => $this->getMaxRedemptions(),
            'times_redeemed' => $this->getTimesRedeemed(),
            'is_active' => $this->getIsActive(),
            'starts_at' => $this->getStartsAt(),
            'expires_at' => $this->getExpiresAt(),
            'duration' => $this->getDuration(),
            'duration_in_months' => $this->getDurationInMonths(),
            'metadata' => $this->getMetadata(),
            'restrictions' => $this->getRestrictions(),
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

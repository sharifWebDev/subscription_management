<?php

namespace App\Dtos;

class RateLimitsDto implements \JsonSerializable
{
    private mixed $id;

    private mixed $subscription_id;

    private mixed $feature_id;

    private mixed $key;

    private int $max_attempts;

    private int $decay_seconds;

    private int $remaining;

    private string $resets_at;

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
        $this->setKey($data['key'] ?? null);
        $this->setMaxAttempts($data['max_attempts'] ?? 0);
        $this->setDecaySeconds($data['decay_seconds'] ?? 0);
        $this->setRemaining($data['remaining'] ?? 0);
        $this->setResetsAt($data['resets_at'] ?? '');
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

    public function setKey(mixed $value): void
    {
        $this->key = $value;
    }

    public function getKey(): mixed
    {
        return $this->key;
    }

    public function setMaxAttempts(int $value): void
    {
        $this->max_attempts = $value;
    }

    public function getMaxAttempts(): int
    {
        return $this->max_attempts;
    }

    public function setDecaySeconds(int $value): void
    {
        $this->decay_seconds = $value;
    }

    public function getDecaySeconds(): int
    {
        return $this->decay_seconds;
    }

    public function setRemaining(int $value): void
    {
        $this->remaining = $value;
    }

    public function getRemaining(): int
    {
        return $this->remaining;
    }

    public function setResetsAt(string $value): void
    {
        $this->resets_at = $value;
    }

    public function getResetsAt(): ?string
    {
        if ($this->resets_at instanceof \DateTimeInterface) {
            return $this->resets_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->resets_at)) {
            $timestamp = strtotime($this->resets_at);
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
            'key' => $this->getKey(),
            'max_attempts' => $this->getMaxAttempts(),
            'decay_seconds' => $this->getDecaySeconds(),
            'remaining' => $this->getRemaining(),
            'resets_at' => $this->getResetsAt(),
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

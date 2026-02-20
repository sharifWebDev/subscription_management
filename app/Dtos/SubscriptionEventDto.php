<?php

namespace App\Dtos;

class SubscriptionEventsDto implements \JsonSerializable
{
    private mixed $id;

    private mixed $subscription_id;

    private mixed $type;

    private ?array $data;

    private ?array $changes;

    private ?mixed $causer_id;

    private ?mixed $causer_type;

    private ?mixed $ip_address;

    private ?string $user_agent;

    private ?array $metadata;

    private string $occurred_at;

    private ?mixed $created_by;

    private ?mixed $updated_by;

    private string $created_at;

    private string $updated_at;

    private ?string $deleted_at;

    public function __construct(array $data = [])
    {
        $this->setId($data['id'] ?? null);
        $this->setSubscriptionId($data['subscription_id'] ?? null);
        $this->setType($data['type'] ?? null);
        $this->setData($data['data'] ?? null);
        $this->setChanges($data['changes'] ?? null);
        $this->setCauserId($data['causer_id'] ?? null);
        $this->setCauserType($data['causer_type'] ?? null);
        $this->setIpAddress($data['ip_address'] ?? null);
        $this->setUserAgent($data['user_agent'] ?? null);
        $this->setMetadata($data['metadata'] ?? null);
        $this->setOccurredAt($data['occurred_at'] ?? null);
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

    public function setType(mixed $value): void
    {
        $this->type = $value;
    }

    public function getType(): mixed
    {
        return $this->type;
    }

    public function setData(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->data = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->data = $value;
        }
    }

    public function getData(): array
    {
        if (is_array($this->data)) {
            return $this->data;
        }

        if (is_string($this->data)) {
            $decoded = json_decode($this->data, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
    }

    public function setChanges(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->changes = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->changes = $value;
        }
    }

    public function getChanges(): array
    {
        if (is_array($this->changes)) {
            return $this->changes;
        }

        if (is_string($this->changes)) {
            $decoded = json_decode($this->changes, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
    }

    public function setCauserId(?mixed $value): void
    {
        $this->causer_id = $value;
    }

    public function getCauserId(): ?mixed
    {
        return $this->causer_id;
    }

    public function setCauserType(?mixed $value): void
    {
        $this->causer_type = $value;
    }

    public function getCauserType(): ?mixed
    {
        return $this->causer_type;
    }

    public function setIpAddress(?mixed $value): void
    {
        $this->ip_address = $value;
    }

    public function getIpAddress(): ?mixed
    {
        return $this->ip_address;
    }

    public function setUserAgent(?string $value): void
    {
        $this->user_agent = $value;
    }

    public function getUserAgent(): ?string
    {
        return $this->user_agent;
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

    public function setOccurredAt(string $value): void
    {
        $this->occurred_at = $value;
    }

    public function getOccurredAt(): ?string
    {
        if ($this->occurred_at instanceof \DateTimeInterface) {
            return $this->occurred_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->occurred_at)) {
            $timestamp = strtotime($this->occurred_at);
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
            'type' => $this->getType(),
            'data' => $this->getData(),
            'changes' => $this->getChanges(),
            'causer_id' => $this->getCauserId(),
            'causer_type' => $this->getCauserType(),
            'ip_address' => $this->getIpAddress(),
            'user_agent' => $this->getUserAgent(),
            'metadata' => $this->getMetadata(),
            'occurred_at' => $this->getOccurredAt(),
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

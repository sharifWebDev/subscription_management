<?php

namespace App\Dtos;

class PaymentAllocationsDto implements \JsonSerializable
{
    private mixed $id;

    private mixed $payment_master_id;

    private mixed $payment_child_id;

    private mixed $payment_transaction_id;

    private mixed $allocatable_type;

    private mixed $allocatable_id;

    private float $amount;

    private ?float $base_amount;

    private float $exchange_rate;

    private mixed $currency;

    private ?mixed $allocation_reference;

    private mixed $allocation_type;

    private bool $is_reversed;

    private ?string $reversed_at;

    private ?mixed $reversal_id;

    private ?array $metadata;

    private ?string $notes;

    private ?mixed $created_by;

    private ?mixed $updated_by;

    private string $created_at;

    private string $updated_at;

    private ?string $deleted_at;

    public function __construct(array $data = [])
    {
        $this->setId($data['id'] ?? null);
        $this->setPaymentMasterId($data['payment_master_id'] ?? null);
        $this->setPaymentChildId($data['payment_child_id'] ?? null);
        $this->setPaymentTransactionId($data['payment_transaction_id'] ?? null);
        $this->setAllocatableType($data['allocatable_type'] ?? null);
        $this->setAllocatableId($data['allocatable_id'] ?? null);
        $this->setAmount($data['amount'] ?? 0);
        $this->setBaseAmount($data['base_amount'] ?? null);
        $this->setExchangeRate($data['exchange_rate'] ?? 1.000000);
        $this->setCurrency($data['currency'] ?? null);
        $this->setAllocationReference($data['allocation_reference'] ?? null);
        $this->setAllocationType($data['allocation_type'] ?? null);
        $this->setIsReversed($data['is_reversed'] ?? false);
        $this->setReversedAt($data['reversed_at'] ?? null);
        $this->setReversalId($data['reversal_id'] ?? null);
        $this->setMetadata($data['metadata'] ?? null);
        $this->setNotes($data['notes'] ?? null);
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

    public function setPaymentMasterId(mixed $value): void
    {
        $this->payment_master_id = $value;
    }

    public function getPaymentMasterId(): mixed
    {
        return $this->payment_master_id;
    }

    public function setPaymentChildId(mixed $value): void
    {
        $this->payment_child_id = $value;
    }

    public function getPaymentChildId(): mixed
    {
        return $this->payment_child_id;
    }

    public function setPaymentTransactionId(mixed $value): void
    {
        $this->payment_transaction_id = $value;
    }

    public function getPaymentTransactionId(): mixed
    {
        return $this->payment_transaction_id;
    }

    public function setAllocatableType(mixed $value): void
    {
        $this->allocatable_type = $value;
    }

    public function getAllocatableType(): mixed
    {
        return $this->allocatable_type;
    }

    public function setAllocatableId(mixed $value): void
    {
        $this->allocatable_id = $value;
    }

    public function getAllocatableId(): mixed
    {
        return $this->allocatable_id;
    }

    public function setAmount(float $value): void
    {
        $this->amount = $value;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setBaseAmount(?float $value): void
    {
        $this->base_amount = $value;
    }

    public function getBaseAmount(): ?float
    {
        return $this->base_amount;
    }

    public function setExchangeRate(float $value): void
    {
        $this->exchange_rate = $value;
    }

    public function getExchangeRate(): float
    {
        return $this->exchange_rate;
    }

    public function setCurrency(mixed $value): void
    {
        $this->currency = $value;
    }

    public function getCurrency(): mixed
    {
        return $this->currency;
    }

    public function setAllocationReference(?mixed $value): void
    {
        $this->allocation_reference = $value;
    }

    public function getAllocationReference(): ?mixed
    {
        return $this->allocation_reference;
    }

    public function setAllocationType(mixed $value): void
    {
        $this->allocation_type = $value;
    }

    public function getAllocationType(): mixed
    {
        return $this->allocation_type;
    }

    public function setIsReversed(bool $value): void
    {
        $this->is_reversed = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    public function getIsReversed(): bool
    {
        return $this->is_reversed ?? false;
    }

    public function setReversedAt(?string $value): void
    {
        $this->reversed_at = $value;
    }

    public function getReversedAt(): ?string
    {
        if ($this->reversed_at instanceof \DateTimeInterface) {
            return $this->reversed_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->reversed_at)) {
            $timestamp = strtotime($this->reversed_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setReversalId(?mixed $value): void
    {
        $this->reversal_id = $value;
    }

    public function getReversalId(): ?mixed
    {
        return $this->reversal_id;
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

    public function setNotes(?string $value): void
    {
        $this->notes = $value;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
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
            'payment_master_id' => $this->getPaymentMasterId(),
            'payment_child_id' => $this->getPaymentChildId(),
            'payment_transaction_id' => $this->getPaymentTransactionId(),
            'allocatable_type' => $this->getAllocatableType(),
            'allocatable_id' => $this->getAllocatableId(),
            'amount' => $this->getAmount(),
            'base_amount' => $this->getBaseAmount(),
            'exchange_rate' => $this->getExchangeRate(),
            'currency' => $this->getCurrency(),
            'allocation_reference' => $this->getAllocationReference(),
            'allocation_type' => $this->getAllocationType(),
            'is_reversed' => $this->getIsReversed(),
            'reversed_at' => $this->getReversedAt(),
            'reversal_id' => $this->getReversalId(),
            'metadata' => $this->getMetadata(),
            'notes' => $this->getNotes(),
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

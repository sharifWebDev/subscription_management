<?php

namespace App\Dtos;

class SubscriptionOrderItemsDto implements \JsonSerializable
{
    private mixed $id;

    private mixed $subscription_order_id;

    private mixed $plan_id;

    private mixed $user_id;

    private ?mixed $recipient_user_id;

    private ?mixed $subscription_id;

    private mixed $plan_name;

    private mixed $billing_cycle;

    private int $quantity;

    private ?array $recipient_info;

    private float $unit_price;

    private float $amount;

    private float $tax_amount;

    private float $discount_amount;

    private float $total_amount;

    private ?string $start_date;

    private ?string $end_date;

    private mixed $subscription_status;

    private ?string $processing_error;

    private ?string $processed_at;

    private ?mixed $created_by;

    private ?mixed $updated_by;

    private string $created_at;

    private string $updated_at;

    private ?string $deleted_at;

    public function __construct(array $data = [])
    {
        $this->setId($data['id'] ?? null);
        $this->setSubscriptionOrderId($data['subscription_order_id'] ?? null);
        $this->setPlanId($data['plan_id'] ?? null);
        $this->setUserId($data['user_id'] ?? null);
        $this->setRecipientUserId($data['recipient_user_id'] ?? null);
        $this->setSubscriptionId($data['subscription_id'] ?? null);
        $this->setPlanName($data['plan_name'] ?? null);
        $this->setBillingCycle($data['billing_cycle'] ?? null);
        $this->setQuantity($data['quantity'] ?? 1);
        $this->setRecipientInfo($data['recipient_info'] ?? null);
        $this->setUnitPrice($data['unit_price'] ?? 0);
        $this->setAmount($data['amount'] ?? 0);
        $this->setTaxAmount($data['tax_amount'] ?? 0.00000000);
        $this->setDiscountAmount($data['discount_amount'] ?? 0.00000000);
        $this->setTotalAmount($data['total_amount'] ?? 0);
        $this->setStartDate($data['start_date'] ?? null);
        $this->setEndDate($data['end_date'] ?? null);
        $this->setSubscriptionStatus($data['subscription_status'] ?? null);
        $this->setProcessingError($data['processing_error'] ?? null);
        $this->setProcessedAt($data['processed_at'] ?? null);
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

    public function setSubscriptionOrderId(mixed $value): void
    {
        $this->subscription_order_id = $value;
    }

    public function getSubscriptionOrderId(): mixed
    {
        return $this->subscription_order_id;
    }

    public function setPlanId(mixed $value): void
    {
        $this->plan_id = $value;
    }

    public function getPlanId(): mixed
    {
        return $this->plan_id;
    }

    public function setUserId(mixed $value): void
    {
        $this->user_id = $value;
    }

    public function getUserId(): mixed
    {
        return $this->user_id;
    }

    public function setRecipientUserId(?mixed $value): void
    {
        $this->recipient_user_id = $value;
    }

    public function getRecipientUserId(): ?mixed
    {
        return $this->recipient_user_id;
    }

    public function setSubscriptionId(?mixed $value): void
    {
        $this->subscription_id = $value;
    }

    public function getSubscriptionId(): ?mixed
    {
        return $this->subscription_id;
    }

    public function setPlanName(mixed $value): void
    {
        $this->plan_name = $value;
    }

    public function getPlanName(): mixed
    {
        return $this->plan_name;
    }

    public function setBillingCycle(mixed $value): void
    {
        $this->billing_cycle = $value;
    }

    public function getBillingCycle(): mixed
    {
        return $this->billing_cycle;
    }

    public function setQuantity(int $value): void
    {
        $this->quantity = $value;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setRecipientInfo(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->recipient_info = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->recipient_info = $value;
        }
    }

    public function getRecipientInfo(): array
    {
        if (is_array($this->recipient_info)) {
            return $this->recipient_info;
        }

        if (is_string($this->recipient_info)) {
            $decoded = json_decode($this->recipient_info, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
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

    public function setTaxAmount(float $value): void
    {
        $this->tax_amount = $value;
    }

    public function getTaxAmount(): float
    {
        return $this->tax_amount;
    }

    public function setDiscountAmount(float $value): void
    {
        $this->discount_amount = $value;
    }

    public function getDiscountAmount(): float
    {
        return $this->discount_amount;
    }

    public function setTotalAmount(float $value): void
    {
        $this->total_amount = $value;
    }

    public function getTotalAmount(): float
    {
        return $this->total_amount;
    }

    public function setStartDate(?string $value): void
    {
        $this->start_date = $value;
    }

    public function getStartDate(): ?string
    {
        return $this->start_date;
    }

    public function setEndDate(?string $value): void
    {
        $this->end_date = $value;
    }

    public function getEndDate(): ?string
    {
        return $this->end_date;
    }

    public function setSubscriptionStatus(mixed $value): void
    {
        $this->subscription_status = $value;
    }

    public function getSubscriptionStatus(): mixed
    {
        return $this->subscription_status;
    }

    public function setProcessingError(?string $value): void
    {
        $this->processing_error = $value;
    }

    public function getProcessingError(): ?string
    {
        return $this->processing_error;
    }

    public function setProcessedAt(?string $value): void
    {
        $this->processed_at = $value;
    }

    public function getProcessedAt(): ?string
    {
        if ($this->processed_at instanceof \DateTimeInterface) {
            return $this->processed_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->processed_at)) {
            $timestamp = strtotime($this->processed_at);
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
            'subscription_order_id' => $this->getSubscriptionOrderId(),
            'plan_id' => $this->getPlanId(),
            'user_id' => $this->getUserId(),
            'recipient_user_id' => $this->getRecipientUserId(),
            'subscription_id' => $this->getSubscriptionId(),
            'plan_name' => $this->getPlanName(),
            'billing_cycle' => $this->getBillingCycle(),
            'quantity' => $this->getQuantity(),
            'recipient_info' => $this->getRecipientInfo(),
            'unit_price' => $this->getUnitPrice(),
            'amount' => $this->getAmount(),
            'tax_amount' => $this->getTaxAmount(),
            'discount_amount' => $this->getDiscountAmount(),
            'total_amount' => $this->getTotalAmount(),
            'start_date' => $this->getStartDate(),
            'end_date' => $this->getEndDate(),
            'subscription_status' => $this->getSubscriptionStatus(),
            'processing_error' => $this->getProcessingError(),
            'processed_at' => $this->getProcessedAt(),
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

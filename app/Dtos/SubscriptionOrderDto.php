<?php

namespace App\Dtos;

class SubscriptionOrdersDto implements \JsonSerializable
{
    private mixed $id;

    private mixed $user_id;

    private ?mixed $payment_master_id;

    private mixed $order_number;

    private mixed $status;

    private mixed $type;

    private float $subtotal;

    private float $tax_amount;

    private float $discount_amount;

    private float $total_amount;

    private mixed $currency;

    private ?array $customer_info;

    private ?array $billing_address;

    private ?string $ordered_at;

    private ?string $processed_at;

    private ?string $cancelled_at;

    private ?mixed $coupon_code;

    private ?array $applied_discounts;

    private ?array $metadata;

    private ?string $notes;

    private ?string $failure_reason;

    private ?mixed $created_by;

    private ?mixed $updated_by;

    private string $created_at;

    private string $updated_at;

    private ?string $deleted_at;

    public function __construct(array $data = [])
    {
        $this->setId($data['id'] ?? null);
        $this->setUserId($data['user_id'] ?? null);
        $this->setPaymentMasterId($data['payment_master_id'] ?? null);
        $this->setOrderNumber($data['order_number'] ?? null);
        $this->setStatus($data['status'] ?? null);
        $this->setType($data['type'] ?? null);
        $this->setSubtotal($data['subtotal'] ?? 0.00000000);
        $this->setTaxAmount($data['tax_amount'] ?? 0.00000000);
        $this->setDiscountAmount($data['discount_amount'] ?? 0.00000000);
        $this->setTotalAmount($data['total_amount'] ?? 0.00000000);
        $this->setCurrency($data['currency'] ?? null);
        $this->setCustomerInfo($data['customer_info'] ?? null);
        $this->setBillingAddress($data['billing_address'] ?? null);
        $this->setOrderedAt($data['ordered_at'] ?? null);
        $this->setProcessedAt($data['processed_at'] ?? null);
        $this->setCancelledAt($data['cancelled_at'] ?? null);
        $this->setCouponCode($data['coupon_code'] ?? null);
        $this->setAppliedDiscounts($data['applied_discounts'] ?? null);
        $this->setMetadata($data['metadata'] ?? null);
        $this->setNotes($data['notes'] ?? null);
        $this->setFailureReason($data['failure_reason'] ?? null);
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

    public function setUserId(mixed $value): void
    {
        $this->user_id = $value;
    }

    public function getUserId(): mixed
    {
        return $this->user_id;
    }

    public function setPaymentMasterId(?mixed $value): void
    {
        $this->payment_master_id = $value;
    }

    public function getPaymentMasterId(): ?mixed
    {
        return $this->payment_master_id;
    }

    public function setOrderNumber(mixed $value): void
    {
        $this->order_number = $value;
    }

    public function getOrderNumber(): mixed
    {
        return $this->order_number;
    }

    public function setStatus(mixed $value): void
    {
        $this->status = $value;
    }

    public function getStatus(): mixed
    {
        return $this->status;
    }

    public function setType(mixed $value): void
    {
        $this->type = $value;
    }

    public function getType(): mixed
    {
        return $this->type;
    }

    public function setSubtotal(float $value): void
    {
        $this->subtotal = $value;
    }

    public function getSubtotal(): float
    {
        return $this->subtotal;
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

    public function setCurrency(mixed $value): void
    {
        $this->currency = $value;
    }

    public function getCurrency(): mixed
    {
        return $this->currency;
    }

    public function setCustomerInfo(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->customer_info = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->customer_info = $value;
        }
    }

    public function getCustomerInfo(): array
    {
        if (is_array($this->customer_info)) {
            return $this->customer_info;
        }

        if (is_string($this->customer_info)) {
            $decoded = json_decode($this->customer_info, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
    }

    public function setBillingAddress(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->billing_address = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->billing_address = $value;
        }
    }

    public function getBillingAddress(): array
    {
        if (is_array($this->billing_address)) {
            return $this->billing_address;
        }

        if (is_string($this->billing_address)) {
            $decoded = json_decode($this->billing_address, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
    }

    public function setOrderedAt(?string $value): void
    {
        $this->ordered_at = $value;
    }

    public function getOrderedAt(): ?string
    {
        if ($this->ordered_at instanceof \DateTimeInterface) {
            return $this->ordered_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->ordered_at)) {
            $timestamp = strtotime($this->ordered_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
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

    public function setCancelledAt(?string $value): void
    {
        $this->cancelled_at = $value;
    }

    public function getCancelledAt(): ?string
    {
        if ($this->cancelled_at instanceof \DateTimeInterface) {
            return $this->cancelled_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->cancelled_at)) {
            $timestamp = strtotime($this->cancelled_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setCouponCode(?mixed $value): void
    {
        $this->coupon_code = $value;
    }

    public function getCouponCode(): ?mixed
    {
        return $this->coupon_code;
    }

    public function setAppliedDiscounts(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->applied_discounts = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->applied_discounts = $value;
        }
    }

    public function getAppliedDiscounts(): array
    {
        if (is_array($this->applied_discounts)) {
            return $this->applied_discounts;
        }

        if (is_string($this->applied_discounts)) {
            $decoded = json_decode($this->applied_discounts, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
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

    public function setFailureReason(?string $value): void
    {
        $this->failure_reason = $value;
    }

    public function getFailureReason(): ?string
    {
        return $this->failure_reason;
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
            'user_id' => $this->getUserId(),
            'payment_master_id' => $this->getPaymentMasterId(),
            'order_number' => $this->getOrderNumber(),
            'status' => $this->getStatus(),
            'type' => $this->getType(),
            'subtotal' => $this->getSubtotal(),
            'tax_amount' => $this->getTaxAmount(),
            'discount_amount' => $this->getDiscountAmount(),
            'total_amount' => $this->getTotalAmount(),
            'currency' => $this->getCurrency(),
            'customer_info' => $this->getCustomerInfo(),
            'billing_address' => $this->getBillingAddress(),
            'ordered_at' => $this->getOrderedAt(),
            'processed_at' => $this->getProcessedAt(),
            'cancelled_at' => $this->getCancelledAt(),
            'coupon_code' => $this->getCouponCode(),
            'applied_discounts' => $this->getAppliedDiscounts(),
            'metadata' => $this->getMetadata(),
            'notes' => $this->getNotes(),
            'failure_reason' => $this->getFailureReason(),
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

<?php

namespace App\Dtos;

class PaymentChildrenDto implements \JsonSerializable
{
    private mixed $id;

    private mixed $payment_master_id;

    private ?mixed $subscription_id;

    private ?mixed $plan_id;

    private ?mixed $invoice_id;

    private mixed $item_type;

    private mixed $item_id;

    private string $description;

    private ?mixed $item_code;

    private float $unit_price;

    private int $quantity;

    private float $amount;

    private float $tax_amount;

    private float $discount_amount;

    private float $total_amount;

    private ?string $period_start;

    private ?string $period_end;

    private ?mixed $billing_cycle;

    private mixed $status;

    private ?string $paid_at;

    private float $allocated_amount;

    private ?bool $is_fully_allocated;

    private ?array $metadata;

    private ?array $tax_breakdown;

    private ?array $discount_breakdown;

    private ?mixed $created_by;

    private ?mixed $updated_by;

    private string $created_at;

    private string $updated_at;

    private ?string $deleted_at;

    public function __construct(array $data = [])
    {
        $this->setId($data['id'] ?? null);
        $this->setPaymentMasterId($data['payment_master_id'] ?? null);
        $this->setSubscriptionId($data['subscription_id'] ?? null);
        $this->setPlanId($data['plan_id'] ?? null);
        $this->setInvoiceId($data['invoice_id'] ?? null);
        $this->setItemType($data['item_type'] ?? null);
        $this->setItemId($data['item_id'] ?? null);
        $this->setDescription($data['description'] ?? '');
        $this->setItemCode($data['item_code'] ?? null);
        $this->setUnitPrice($data['unit_price'] ?? 0.00000000);
        $this->setQuantity($data['quantity'] ?? 1);
        $this->setAmount($data['amount'] ?? 0.00000000);
        $this->setTaxAmount($data['tax_amount'] ?? 0.00000000);
        $this->setDiscountAmount($data['discount_amount'] ?? 0.00000000);
        $this->setTotalAmount($data['total_amount'] ?? 0.00000000);
        $this->setPeriodStart($data['period_start'] ?? null);
        $this->setPeriodEnd($data['period_end'] ?? null);
        $this->setBillingCycle($data['billing_cycle'] ?? null);
        $this->setStatus($data['status'] ?? null);
        $this->setPaidAt($data['paid_at'] ?? null);
        $this->setAllocatedAmount($data['allocated_amount'] ?? 0.00000000);
        $this->setIsFullyAllocated($data['is_fully_allocated'] ?? null);
        $this->setMetadata($data['metadata'] ?? null);
        $this->setTaxBreakdown($data['tax_breakdown'] ?? null);
        $this->setDiscountBreakdown($data['discount_breakdown'] ?? null);
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

    public function setSubscriptionId(?mixed $value): void
    {
        $this->subscription_id = $value;
    }

    public function getSubscriptionId(): ?mixed
    {
        return $this->subscription_id;
    }

    public function setPlanId(?mixed $value): void
    {
        $this->plan_id = $value;
    }

    public function getPlanId(): ?mixed
    {
        return $this->plan_id;
    }

    public function setInvoiceId(?mixed $value): void
    {
        $this->invoice_id = $value;
    }

    public function getInvoiceId(): ?mixed
    {
        return $this->invoice_id;
    }

    public function setItemType(mixed $value): void
    {
        $this->item_type = $value;
    }

    public function getItemType(): mixed
    {
        return $this->item_type;
    }

    public function setItemId(mixed $value): void
    {
        $this->item_id = $value;
    }

    public function getItemId(): mixed
    {
        return $this->item_id;
    }

    public function setDescription(string $value): void
    {
        $this->description = $value;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setItemCode(?mixed $value): void
    {
        $this->item_code = $value;
    }

    public function getItemCode(): ?mixed
    {
        return $this->item_code;
    }

    public function setUnitPrice(float $value): void
    {
        $this->unit_price = $value;
    }

    public function getUnitPrice(): float
    {
        return $this->unit_price;
    }

    public function setQuantity(int $value): void
    {
        $this->quantity = $value;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
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

    public function setPeriodStart(?string $value): void
    {
        $this->period_start = $value;
    }

    public function getPeriodStart(): ?string
    {
        return $this->period_start;
    }

    public function setPeriodEnd(?string $value): void
    {
        $this->period_end = $value;
    }

    public function getPeriodEnd(): ?string
    {
        return $this->period_end;
    }

    public function setBillingCycle(?mixed $value): void
    {
        $this->billing_cycle = $value;
    }

    public function getBillingCycle(): ?mixed
    {
        return $this->billing_cycle;
    }

    public function setStatus(mixed $value): void
    {
        $this->status = $value;
    }

    public function getStatus(): mixed
    {
        return $this->status;
    }

    public function setPaidAt(?string $value): void
    {
        $this->paid_at = $value;
    }

    public function getPaidAt(): ?string
    {
        if ($this->paid_at instanceof \DateTimeInterface) {
            return $this->paid_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->paid_at)) {
            $timestamp = strtotime($this->paid_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setAllocatedAmount(float $value): void
    {
        $this->allocated_amount = $value;
    }

    public function getAllocatedAmount(): float
    {
        return $this->allocated_amount;
    }

    public function setIsFullyAllocated(?bool $value): void
    {
        $this->is_fully_allocated = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    public function getIsFullyAllocated(): ?bool
    {
        return $this->is_fully_allocated;
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

    public function setTaxBreakdown(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->tax_breakdown = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->tax_breakdown = $value;
        }
    }

    public function getTaxBreakdown(): array
    {
        if (is_array($this->tax_breakdown)) {
            return $this->tax_breakdown;
        }

        if (is_string($this->tax_breakdown)) {
            $decoded = json_decode($this->tax_breakdown, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
    }

    public function setDiscountBreakdown(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->discount_breakdown = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->discount_breakdown = $value;
        }
    }

    public function getDiscountBreakdown(): array
    {
        if (is_array($this->discount_breakdown)) {
            return $this->discount_breakdown;
        }

        if (is_string($this->discount_breakdown)) {
            $decoded = json_decode($this->discount_breakdown, true);

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
            'payment_master_id' => $this->getPaymentMasterId(),
            'subscription_id' => $this->getSubscriptionId(),
            'plan_id' => $this->getPlanId(),
            'invoice_id' => $this->getInvoiceId(),
            'item_type' => $this->getItemType(),
            'item_id' => $this->getItemId(),
            'description' => $this->getDescription(),
            'item_code' => $this->getItemCode(),
            'unit_price' => $this->getUnitPrice(),
            'quantity' => $this->getQuantity(),
            'amount' => $this->getAmount(),
            'tax_amount' => $this->getTaxAmount(),
            'discount_amount' => $this->getDiscountAmount(),
            'total_amount' => $this->getTotalAmount(),
            'period_start' => $this->getPeriodStart(),
            'period_end' => $this->getPeriodEnd(),
            'billing_cycle' => $this->getBillingCycle(),
            'status' => $this->getStatus(),
            'paid_at' => $this->getPaidAt(),
            'allocated_amount' => $this->getAllocatedAmount(),
            'is_fully_allocated' => $this->getIsFullyAllocated(),
            'metadata' => $this->getMetadata(),
            'tax_breakdown' => $this->getTaxBreakdown(),
            'discount_breakdown' => $this->getDiscountBreakdown(),
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

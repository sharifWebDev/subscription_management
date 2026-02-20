<?php

namespace App\Dtos;

class PaymentMastersDto implements \JsonSerializable
{
    private mixed $id;

    private mixed $user_id;

    private mixed $payment_number;

    private mixed $type;

    private mixed $status;

    private float $total_amount;

    private float $subtotal;

    private float $tax_amount;

    private float $discount_amount;

    private float $fee_amount;

    private float $net_amount;

    private float $paid_amount;

    private ?float $due_amount;

    private mixed $currency;

    private float $exchange_rate;

    private mixed $base_currency;

    private ?float $base_amount;

    private ?mixed $payment_method;

    private ?array $payment_method_details;

    private ?mixed $payment_gateway;

    private bool $is_installment;

    private ?int $installment_count;

    private ?mixed $installment_frequency;

    private ?string $payment_date;

    private ?string $due_date;

    private ?string $paid_at;

    private ?string $cancelled_at;

    private ?string $expires_at;

    private ?mixed $customer_reference;

    private ?mixed $bank_reference;

    private ?mixed $gateway_reference;

    private ?array $metadata;

    private ?array $custom_fields;

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
        $this->setPaymentNumber($data['payment_number'] ?? null);
        $this->setType($data['type'] ?? null);
        $this->setStatus($data['status'] ?? null);
        $this->setTotalAmount($data['total_amount'] ?? 0.00000000);
        $this->setSubtotal($data['subtotal'] ?? 0.00000000);
        $this->setTaxAmount($data['tax_amount'] ?? 0.00000000);
        $this->setDiscountAmount($data['discount_amount'] ?? 0.00000000);
        $this->setFeeAmount($data['fee_amount'] ?? 0.00000000);
        $this->setNetAmount($data['net_amount'] ?? 0.00000000);
        $this->setPaidAmount($data['paid_amount'] ?? 0.00000000);
        $this->setDueAmount($data['due_amount'] ?? null);
        $this->setCurrency($data['currency'] ?? null);
        $this->setExchangeRate($data['exchange_rate'] ?? 1.000000);
        $this->setBaseCurrency($data['base_currency'] ?? null);
        $this->setBaseAmount($data['base_amount'] ?? null);
        $this->setPaymentMethod($data['payment_method'] ?? null);
        $this->setPaymentMethodDetails($data['payment_method_details'] ?? null);
        $this->setPaymentGateway($data['payment_gateway'] ?? null);
        $this->setIsInstallment($data['is_installment'] ?? false);
        $this->setInstallmentCount($data['installment_count'] ?? null);
        $this->setInstallmentFrequency($data['installment_frequency'] ?? null);
        $this->setPaymentDate($data['payment_date'] ?? null);
        $this->setDueDate($data['due_date'] ?? null);
        $this->setPaidAt($data['paid_at'] ?? null);
        $this->setCancelledAt($data['cancelled_at'] ?? null);
        $this->setExpiresAt($data['expires_at'] ?? null);
        $this->setCustomerReference($data['customer_reference'] ?? null);
        $this->setBankReference($data['bank_reference'] ?? null);
        $this->setGatewayReference($data['gateway_reference'] ?? null);
        $this->setMetadata($data['metadata'] ?? null);
        $this->setCustomFields($data['custom_fields'] ?? null);
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

    public function setPaymentNumber(mixed $value): void
    {
        $this->payment_number = $value;
    }

    public function getPaymentNumber(): mixed
    {
        return $this->payment_number;
    }

    public function setType(mixed $value): void
    {
        $this->type = $value;
    }

    public function getType(): mixed
    {
        return $this->type;
    }

    public function setStatus(mixed $value): void
    {
        $this->status = $value;
    }

    public function getStatus(): mixed
    {
        return $this->status;
    }

    public function setTotalAmount(float $value): void
    {
        $this->total_amount = $value;
    }

    public function getTotalAmount(): float
    {
        return $this->total_amount;
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

    public function setFeeAmount(float $value): void
    {
        $this->fee_amount = $value;
    }

    public function getFeeAmount(): float
    {
        return $this->fee_amount;
    }

    public function setNetAmount(float $value): void
    {
        $this->net_amount = $value;
    }

    public function getNetAmount(): float
    {
        return $this->net_amount;
    }

    public function setPaidAmount(float $value): void
    {
        $this->paid_amount = $value;
    }

    public function getPaidAmount(): float
    {
        return $this->paid_amount;
    }

    public function setDueAmount(?float $value): void
    {
        $this->due_amount = $value;
    }

    public function getDueAmount(): ?float
    {
        return $this->due_amount;
    }

    public function setCurrency(mixed $value): void
    {
        $this->currency = $value;
    }

    public function getCurrency(): mixed
    {
        return $this->currency;
    }

    public function setExchangeRate(float $value): void
    {
        $this->exchange_rate = $value;
    }

    public function getExchangeRate(): float
    {
        return $this->exchange_rate;
    }

    public function setBaseCurrency(mixed $value): void
    {
        $this->base_currency = $value;
    }

    public function getBaseCurrency(): mixed
    {
        return $this->base_currency;
    }

    public function setBaseAmount(?float $value): void
    {
        $this->base_amount = $value;
    }

    public function getBaseAmount(): ?float
    {
        return $this->base_amount;
    }

    public function setPaymentMethod(?mixed $value): void
    {
        $this->payment_method = $value;
    }

    public function getPaymentMethod(): ?mixed
    {
        return $this->payment_method;
    }

    public function setPaymentMethodDetails(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->payment_method_details = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->payment_method_details = $value;
        }
    }

    public function getPaymentMethodDetails(): array
    {
        if (is_array($this->payment_method_details)) {
            return $this->payment_method_details;
        }

        if (is_string($this->payment_method_details)) {
            $decoded = json_decode($this->payment_method_details, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
    }

    public function setPaymentGateway(?mixed $value): void
    {
        $this->payment_gateway = $value;
    }

    public function getPaymentGateway(): ?mixed
    {
        return $this->payment_gateway;
    }

    public function setIsInstallment(bool $value): void
    {
        $this->is_installment = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    public function getIsInstallment(): bool
    {
        return $this->is_installment ?? false;
    }

    public function setInstallmentCount(?int $value): void
    {
        $this->installment_count = $value;
    }

    public function getInstallmentCount(): ?int
    {
        return $this->installment_count;
    }

    public function setInstallmentFrequency(?mixed $value): void
    {
        $this->installment_frequency = $value;
    }

    public function getInstallmentFrequency(): ?mixed
    {
        return $this->installment_frequency;
    }

    public function setPaymentDate(?string $value): void
    {
        $this->payment_date = $value;
    }

    public function getPaymentDate(): ?string
    {
        if ($this->payment_date instanceof \DateTimeInterface) {
            return $this->payment_date->format('Y-m-d H:i:s');
        }

        if (is_string($this->payment_date)) {
            $timestamp = strtotime($this->payment_date);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setDueDate(?string $value): void
    {
        $this->due_date = $value;
    }

    public function getDueDate(): ?string
    {
        if ($this->due_date instanceof \DateTimeInterface) {
            return $this->due_date->format('Y-m-d H:i:s');
        }

        if (is_string($this->due_date)) {
            $timestamp = strtotime($this->due_date);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
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

    public function setCustomerReference(?mixed $value): void
    {
        $this->customer_reference = $value;
    }

    public function getCustomerReference(): ?mixed
    {
        return $this->customer_reference;
    }

    public function setBankReference(?mixed $value): void
    {
        $this->bank_reference = $value;
    }

    public function getBankReference(): ?mixed
    {
        return $this->bank_reference;
    }

    public function setGatewayReference(?mixed $value): void
    {
        $this->gateway_reference = $value;
    }

    public function getGatewayReference(): ?mixed
    {
        return $this->gateway_reference;
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

    public function setCustomFields(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->custom_fields = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->custom_fields = $value;
        }
    }

    public function getCustomFields(): array
    {
        if (is_array($this->custom_fields)) {
            return $this->custom_fields;
        }

        if (is_string($this->custom_fields)) {
            $decoded = json_decode($this->custom_fields, true);

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
            'payment_number' => $this->getPaymentNumber(),
            'type' => $this->getType(),
            'status' => $this->getStatus(),
            'total_amount' => $this->getTotalAmount(),
            'subtotal' => $this->getSubtotal(),
            'tax_amount' => $this->getTaxAmount(),
            'discount_amount' => $this->getDiscountAmount(),
            'fee_amount' => $this->getFeeAmount(),
            'net_amount' => $this->getNetAmount(),
            'paid_amount' => $this->getPaidAmount(),
            'due_amount' => $this->getDueAmount(),
            'currency' => $this->getCurrency(),
            'exchange_rate' => $this->getExchangeRate(),
            'base_currency' => $this->getBaseCurrency(),
            'base_amount' => $this->getBaseAmount(),
            'payment_method' => $this->getPaymentMethod(),
            'payment_method_details' => $this->getPaymentMethodDetails(),
            'payment_gateway' => $this->getPaymentGateway(),
            'is_installment' => $this->getIsInstallment(),
            'installment_count' => $this->getInstallmentCount(),
            'installment_frequency' => $this->getInstallmentFrequency(),
            'payment_date' => $this->getPaymentDate(),
            'due_date' => $this->getDueDate(),
            'paid_at' => $this->getPaidAt(),
            'cancelled_at' => $this->getCancelledAt(),
            'expires_at' => $this->getExpiresAt(),
            'customer_reference' => $this->getCustomerReference(),
            'bank_reference' => $this->getBankReference(),
            'gateway_reference' => $this->getGatewayReference(),
            'metadata' => $this->getMetadata(),
            'custom_fields' => $this->getCustomFields(),
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

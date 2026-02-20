<?php

namespace App\Dtos;

class PaymentsDto implements \JsonSerializable
{
    private mixed $id;

    private ?mixed $invoice_id;

    private mixed $user_id;

    private ?mixed $external_id;

    private mixed $type;

    private mixed $status;

    private float $amount;

    private float $fee;

    private ?float $net;

    private mixed $currency;

    private mixed $gateway;

    private ?array $gateway_response;

    private ?array $payment_method;

    private ?string $processed_at;

    private ?string $refunded_at;

    private ?array $metadata;

    private ?array $fraud_indicators;

    private ?mixed $created_by;

    private ?mixed $updated_by;

    private string $created_at;

    private string $updated_at;

    private ?string $deleted_at;

    public function __construct(array $data = [])
    {
        $this->setId($data['id'] ?? null);
        $this->setInvoiceId($data['invoice_id'] ?? null);
        $this->setUserId($data['user_id'] ?? null);
        $this->setExternalId($data['external_id'] ?? null);
        $this->setType($data['type'] ?? null);
        $this->setStatus($data['status'] ?? null);
        $this->setAmount($data['amount'] ?? 0);
        $this->setFee($data['fee'] ?? 0.00000000);
        $this->setNet($data['net'] ?? null);
        $this->setCurrency($data['currency'] ?? null);
        $this->setGateway($data['gateway'] ?? null);
        $this->setGatewayResponse($data['gateway_response'] ?? null);
        $this->setPaymentMethod($data['payment_method'] ?? null);
        $this->setProcessedAt($data['processed_at'] ?? null);
        $this->setRefundedAt($data['refunded_at'] ?? null);
        $this->setMetadata($data['metadata'] ?? null);
        $this->setFraudIndicators($data['fraud_indicators'] ?? null);
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

    public function setInvoiceId(?mixed $value): void
    {
        $this->invoice_id = $value;
    }

    public function getInvoiceId(): ?mixed
    {
        return $this->invoice_id;
    }

    public function setUserId(mixed $value): void
    {
        $this->user_id = $value;
    }

    public function getUserId(): mixed
    {
        return $this->user_id;
    }

    public function setExternalId(?mixed $value): void
    {
        $this->external_id = $value;
    }

    public function getExternalId(): ?mixed
    {
        return $this->external_id;
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

    public function setAmount(float $value): void
    {
        $this->amount = $value;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setFee(float $value): void
    {
        $this->fee = $value;
    }

    public function getFee(): float
    {
        return $this->fee;
    }

    public function setNet(?float $value): void
    {
        $this->net = $value;
    }

    public function getNet(): ?float
    {
        return $this->net;
    }

    public function setCurrency(mixed $value): void
    {
        $this->currency = $value;
    }

    public function getCurrency(): mixed
    {
        return $this->currency;
    }

    public function setGateway(mixed $value): void
    {
        $this->gateway = $value;
    }

    public function getGateway(): mixed
    {
        return $this->gateway;
    }

    public function setGatewayResponse(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->gateway_response = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->gateway_response = $value;
        }
    }

    public function getGatewayResponse(): array
    {
        if (is_array($this->gateway_response)) {
            return $this->gateway_response;
        }

        if (is_string($this->gateway_response)) {
            $decoded = json_decode($this->gateway_response, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
    }

    public function setPaymentMethod(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->payment_method = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->payment_method = $value;
        }
    }

    public function getPaymentMethod(): array
    {
        if (is_array($this->payment_method)) {
            return $this->payment_method;
        }

        if (is_string($this->payment_method)) {
            $decoded = json_decode($this->payment_method, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
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

    public function setRefundedAt(?string $value): void
    {
        $this->refunded_at = $value;
    }

    public function getRefundedAt(): ?string
    {
        if ($this->refunded_at instanceof \DateTimeInterface) {
            return $this->refunded_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->refunded_at)) {
            $timestamp = strtotime($this->refunded_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
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

    public function setFraudIndicators(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->fraud_indicators = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->fraud_indicators = $value;
        }
    }

    public function getFraudIndicators(): array
    {
        if (is_array($this->fraud_indicators)) {
            return $this->fraud_indicators;
        }

        if (is_string($this->fraud_indicators)) {
            $decoded = json_decode($this->fraud_indicators, true);

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
            'invoice_id' => $this->getInvoiceId(),
            'user_id' => $this->getUserId(),
            'external_id' => $this->getExternalId(),
            'type' => $this->getType(),
            'status' => $this->getStatus(),
            'amount' => $this->getAmount(),
            'fee' => $this->getFee(),
            'net' => $this->getNet(),
            'currency' => $this->getCurrency(),
            'gateway' => $this->getGateway(),
            'gateway_response' => $this->getGatewayResponse(),
            'payment_method' => $this->getPaymentMethod(),
            'processed_at' => $this->getProcessedAt(),
            'refunded_at' => $this->getRefundedAt(),
            'metadata' => $this->getMetadata(),
            'fraud_indicators' => $this->getFraudIndicators(),
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

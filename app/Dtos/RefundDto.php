<?php

namespace App\Dtos;

class RefundsDto implements \JsonSerializable
{
    private mixed $id;

    private mixed $payment_master_id;

    private mixed $payment_transaction_id;

    private mixed $user_id;

    private mixed $refund_number;

    private mixed $type;

    private mixed $status;

    private mixed $initiated_by;

    private float $amount;

    private float $fee;

    private ?float $net_amount;

    private mixed $currency;

    private float $exchange_rate;

    private mixed $reason;

    private ?string $reason_details;

    private ?string $customer_comments;

    private string $requested_at;

    private ?string $approved_at;

    private ?mixed $approved_by;

    private ?string $processed_at;

    private ?string $completed_at;

    private ?string $failed_at;

    private ?mixed $gateway_refund_id;

    private ?array $gateway_response;

    private ?array $metadata;

    private ?array $documents;

    private ?mixed $processed_by;

    private ?string $rejection_reason;

    private ?mixed $created_by;

    private ?mixed $updated_by;

    private string $created_at;

    private string $updated_at;

    private ?string $deleted_at;

    public function __construct(array $data = [])
    {
        $this->setId($data['id'] ?? null);
        $this->setPaymentMasterId($data['payment_master_id'] ?? null);
        $this->setPaymentTransactionId($data['payment_transaction_id'] ?? null);
        $this->setUserId($data['user_id'] ?? null);
        $this->setRefundNumber($data['refund_number'] ?? null);
        $this->setType($data['type'] ?? null);
        $this->setStatus($data['status'] ?? null);
        $this->setInitiatedBy($data['initiated_by'] ?? null);
        $this->setAmount($data['amount'] ?? 0);
        $this->setFee($data['fee'] ?? 0.00000000);
        $this->setNetAmount($data['net_amount'] ?? null);
        $this->setCurrency($data['currency'] ?? null);
        $this->setExchangeRate($data['exchange_rate'] ?? 1.000000);
        $this->setReason($data['reason'] ?? null);
        $this->setReasonDetails($data['reason_details'] ?? null);
        $this->setCustomerComments($data['customer_comments'] ?? null);
        $this->setRequestedAt($data['requested_at'] ?? null);
        $this->setApprovedAt($data['approved_at'] ?? null);
        $this->setApprovedBy($data['approved_by'] ?? null);
        $this->setProcessedAt($data['processed_at'] ?? null);
        $this->setCompletedAt($data['completed_at'] ?? null);
        $this->setFailedAt($data['failed_at'] ?? null);
        $this->setGatewayRefundId($data['gateway_refund_id'] ?? null);
        $this->setGatewayResponse($data['gateway_response'] ?? null);
        $this->setMetadata($data['metadata'] ?? null);
        $this->setDocuments($data['documents'] ?? null);
        $this->setProcessedBy($data['processed_by'] ?? null);
        $this->setRejectionReason($data['rejection_reason'] ?? null);
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

    public function setPaymentTransactionId(mixed $value): void
    {
        $this->payment_transaction_id = $value;
    }

    public function getPaymentTransactionId(): mixed
    {
        return $this->payment_transaction_id;
    }

    public function setUserId(mixed $value): void
    {
        $this->user_id = $value;
    }

    public function getUserId(): mixed
    {
        return $this->user_id;
    }

    public function setRefundNumber(mixed $value): void
    {
        $this->refund_number = $value;
    }

    public function getRefundNumber(): mixed
    {
        return $this->refund_number;
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

    public function setInitiatedBy(mixed $value): void
    {
        $this->initiated_by = $value;
    }

    public function getInitiatedBy(): mixed
    {
        return $this->initiated_by;
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

    public function setNetAmount(?float $value): void
    {
        $this->net_amount = $value;
    }

    public function getNetAmount(): ?float
    {
        return $this->net_amount;
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

    public function setReason(mixed $value): void
    {
        $this->reason = $value;
    }

    public function getReason(): mixed
    {
        return $this->reason;
    }

    public function setReasonDetails(?string $value): void
    {
        $this->reason_details = $value;
    }

    public function getReasonDetails(): ?string
    {
        return $this->reason_details;
    }

    public function setCustomerComments(?string $value): void
    {
        $this->customer_comments = $value;
    }

    public function getCustomerComments(): ?string
    {
        return $this->customer_comments;
    }

    public function setRequestedAt(string $value): void
    {
        $this->requested_at = $value;
    }

    public function getRequestedAt(): ?string
    {
        if ($this->requested_at instanceof \DateTimeInterface) {
            return $this->requested_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->requested_at)) {
            $timestamp = strtotime($this->requested_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setApprovedAt(?string $value): void
    {
        $this->approved_at = $value;
    }

    public function getApprovedAt(): ?string
    {
        if ($this->approved_at instanceof \DateTimeInterface) {
            return $this->approved_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->approved_at)) {
            $timestamp = strtotime($this->approved_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setApprovedBy(?mixed $value): void
    {
        $this->approved_by = $value;
    }

    public function getApprovedBy(): ?mixed
    {
        return $this->approved_by;
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

    public function setCompletedAt(?string $value): void
    {
        $this->completed_at = $value;
    }

    public function getCompletedAt(): ?string
    {
        if ($this->completed_at instanceof \DateTimeInterface) {
            return $this->completed_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->completed_at)) {
            $timestamp = strtotime($this->completed_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setFailedAt(?string $value): void
    {
        $this->failed_at = $value;
    }

    public function getFailedAt(): ?string
    {
        if ($this->failed_at instanceof \DateTimeInterface) {
            return $this->failed_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->failed_at)) {
            $timestamp = strtotime($this->failed_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setGatewayRefundId(?mixed $value): void
    {
        $this->gateway_refund_id = $value;
    }

    public function getGatewayRefundId(): ?mixed
    {
        return $this->gateway_refund_id;
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

    public function setDocuments(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->documents = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->documents = $value;
        }
    }

    public function getDocuments(): array
    {
        if (is_array($this->documents)) {
            return $this->documents;
        }

        if (is_string($this->documents)) {
            $decoded = json_decode($this->documents, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
    }

    public function setProcessedBy(?mixed $value): void
    {
        $this->processed_by = $value;
    }

    public function getProcessedBy(): ?mixed
    {
        return $this->processed_by;
    }

    public function setRejectionReason(?string $value): void
    {
        $this->rejection_reason = $value;
    }

    public function getRejectionReason(): ?string
    {
        return $this->rejection_reason;
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
            'payment_transaction_id' => $this->getPaymentTransactionId(),
            'user_id' => $this->getUserId(),
            'refund_number' => $this->getRefundNumber(),
            'type' => $this->getType(),
            'status' => $this->getStatus(),
            'initiated_by' => $this->getInitiatedBy(),
            'amount' => $this->getAmount(),
            'fee' => $this->getFee(),
            'net_amount' => $this->getNetAmount(),
            'currency' => $this->getCurrency(),
            'exchange_rate' => $this->getExchangeRate(),
            'reason' => $this->getReason(),
            'reason_details' => $this->getReasonDetails(),
            'customer_comments' => $this->getCustomerComments(),
            'requested_at' => $this->getRequestedAt(),
            'approved_at' => $this->getApprovedAt(),
            'approved_by' => $this->getApprovedBy(),
            'processed_at' => $this->getProcessedAt(),
            'completed_at' => $this->getCompletedAt(),
            'failed_at' => $this->getFailedAt(),
            'gateway_refund_id' => $this->getGatewayRefundId(),
            'gateway_response' => $this->getGatewayResponse(),
            'metadata' => $this->getMetadata(),
            'documents' => $this->getDocuments(),
            'processed_by' => $this->getProcessedBy(),
            'rejection_reason' => $this->getRejectionReason(),
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

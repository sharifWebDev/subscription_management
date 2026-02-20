<?php

namespace App\Dtos;

class PaymentWebhookLogsDto implements \JsonSerializable
{
    private mixed $id;

    private ?mixed $payment_gateway_id;

    private ?mixed $payment_transaction_id;

    private mixed $gateway;

    private mixed $event_type;

    private ?mixed $webhook_id;

    private ?mixed $reference_id;

    private ?array $payload;

    private ?array $headers;

    private ?int $response_code;

    private ?string $response_body;

    private mixed $status;

    private ?string $processing_error;

    private int $retry_count;

    private ?string $next_retry_at;

    private string $received_at;

    private ?string $processed_at;

    private ?mixed $ip_address;

    private bool $is_verified;

    private ?mixed $verification_error;

    private ?mixed $created_by;

    private ?mixed $updated_by;

    private string $created_at;

    private string $updated_at;

    private ?string $deleted_at;

    public function __construct(array $data = [])
    {
        $this->setId($data['id'] ?? null);
        $this->setPaymentGatewayId($data['payment_gateway_id'] ?? null);
        $this->setPaymentTransactionId($data['payment_transaction_id'] ?? null);
        $this->setGateway($data['gateway'] ?? null);
        $this->setEventType($data['event_type'] ?? null);
        $this->setWebhookId($data['webhook_id'] ?? null);
        $this->setReferenceId($data['reference_id'] ?? null);
        $this->setPayload($data['payload'] ?? null);
        $this->setHeaders($data['headers'] ?? null);
        $this->setResponseCode($data['response_code'] ?? null);
        $this->setResponseBody($data['response_body'] ?? null);
        $this->setStatus($data['status'] ?? null);
        $this->setProcessingError($data['processing_error'] ?? null);
        $this->setRetryCount($data['retry_count'] ?? 0);
        $this->setNextRetryAt($data['next_retry_at'] ?? null);
        $this->setReceivedAt($data['received_at'] ?? null);
        $this->setProcessedAt($data['processed_at'] ?? null);
        $this->setIpAddress($data['ip_address'] ?? null);
        $this->setIsVerified($data['is_verified'] ?? false);
        $this->setVerificationError($data['verification_error'] ?? null);
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

    public function setPaymentGatewayId(?mixed $value): void
    {
        $this->payment_gateway_id = $value;
    }

    public function getPaymentGatewayId(): ?mixed
    {
        return $this->payment_gateway_id;
    }

    public function setPaymentTransactionId(?mixed $value): void
    {
        $this->payment_transaction_id = $value;
    }

    public function getPaymentTransactionId(): ?mixed
    {
        return $this->payment_transaction_id;
    }

    public function setGateway(mixed $value): void
    {
        $this->gateway = $value;
    }

    public function getGateway(): mixed
    {
        return $this->gateway;
    }

    public function setEventType(mixed $value): void
    {
        $this->event_type = $value;
    }

    public function getEventType(): mixed
    {
        return $this->event_type;
    }

    public function setWebhookId(?mixed $value): void
    {
        $this->webhook_id = $value;
    }

    public function getWebhookId(): ?mixed
    {
        return $this->webhook_id;
    }

    public function setReferenceId(?mixed $value): void
    {
        $this->reference_id = $value;
    }

    public function getReferenceId(): ?mixed
    {
        return $this->reference_id;
    }

    public function setPayload(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->payload = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->payload = $value;
        }
    }

    public function getPayload(): array
    {
        if (is_array($this->payload)) {
            return $this->payload;
        }

        if (is_string($this->payload)) {
            $decoded = json_decode($this->payload, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
    }

    public function setHeaders(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->headers = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->headers = $value;
        }
    }

    public function getHeaders(): array
    {
        if (is_array($this->headers)) {
            return $this->headers;
        }

        if (is_string($this->headers)) {
            $decoded = json_decode($this->headers, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
    }

    public function setResponseCode(?int $value): void
    {
        $this->response_code = $value;
    }

    public function getResponseCode(): ?int
    {
        return $this->response_code;
    }

    public function setResponseBody(?string $value): void
    {
        $this->response_body = $value;
    }

    public function getResponseBody(): ?string
    {
        return $this->response_body;
    }

    public function setStatus(mixed $value): void
    {
        $this->status = $value;
    }

    public function getStatus(): mixed
    {
        return $this->status;
    }

    public function setProcessingError(?string $value): void
    {
        $this->processing_error = $value;
    }

    public function getProcessingError(): ?string
    {
        return $this->processing_error;
    }

    public function setRetryCount(int $value): void
    {
        $this->retry_count = $value;
    }

    public function getRetryCount(): int
    {
        return $this->retry_count;
    }

    public function setNextRetryAt(?string $value): void
    {
        $this->next_retry_at = $value;
    }

    public function getNextRetryAt(): ?string
    {
        if ($this->next_retry_at instanceof \DateTimeInterface) {
            return $this->next_retry_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->next_retry_at)) {
            $timestamp = strtotime($this->next_retry_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setReceivedAt(string $value): void
    {
        $this->received_at = $value;
    }

    public function getReceivedAt(): ?string
    {
        if ($this->received_at instanceof \DateTimeInterface) {
            return $this->received_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->received_at)) {
            $timestamp = strtotime($this->received_at);
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

    public function setIpAddress(?mixed $value): void
    {
        $this->ip_address = $value;
    }

    public function getIpAddress(): ?mixed
    {
        return $this->ip_address;
    }

    public function setIsVerified(bool $value): void
    {
        $this->is_verified = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    public function getIsVerified(): bool
    {
        return $this->is_verified ?? false;
    }

    public function setVerificationError(?mixed $value): void
    {
        $this->verification_error = $value;
    }

    public function getVerificationError(): ?mixed
    {
        return $this->verification_error;
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
            'payment_gateway_id' => $this->getPaymentGatewayId(),
            'payment_transaction_id' => $this->getPaymentTransactionId(),
            'gateway' => $this->getGateway(),
            'event_type' => $this->getEventType(),
            'webhook_id' => $this->getWebhookId(),
            'reference_id' => $this->getReferenceId(),
            'payload' => $this->getPayload(),
            'headers' => $this->getHeaders(),
            'response_code' => $this->getResponseCode(),
            'response_body' => $this->getResponseBody(),
            'status' => $this->getStatus(),
            'processing_error' => $this->getProcessingError(),
            'retry_count' => $this->getRetryCount(),
            'next_retry_at' => $this->getNextRetryAt(),
            'received_at' => $this->getReceivedAt(),
            'processed_at' => $this->getProcessedAt(),
            'ip_address' => $this->getIpAddress(),
            'is_verified' => $this->getIsVerified(),
            'verification_error' => $this->getVerificationError(),
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

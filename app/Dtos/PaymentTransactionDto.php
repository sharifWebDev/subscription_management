<?php

namespace App\Dtos;

class PaymentTransactionsDto implements \JsonSerializable
{
    private mixed $id;

    private mixed $payment_master_id;

    private ?mixed $payment_child_id;

    private mixed $transaction_id;

    private ?mixed $reference_id;

    private mixed $type;

    private mixed $payment_method;

    private mixed $payment_gateway;

    private ?array $gateway_response;

    private ?array $payment_method_details;

    private float $amount;

    private float $fee;

    private float $tax;

    private ?float $net_amount;

    private mixed $currency;

    private float $exchange_rate;

    private mixed $status;

    private ?mixed $card_last4;

    private ?mixed $card_brand;

    private ?mixed $card_country;

    private ?int $card_exp_month;

    private ?int $card_exp_year;

    private ?mixed $bank_name;

    private ?mixed $bank_account_last4;

    private ?mixed $bank_routing_number;

    private ?mixed $wallet_type;

    private ?mixed $wallet_number;

    private ?mixed $wallet_transaction_id;

    private ?int $installment_number;

    private ?int $total_installments;

    private string $initiated_at;

    private ?string $authorized_at;

    private ?string $captured_at;

    private ?string $completed_at;

    private ?string $failed_at;

    private ?string $refunded_at;

    private ?array $fraud_indicators;

    private ?float $risk_score;

    private bool $requires_review;

    private ?array $metadata;

    private ?array $custom_fields;

    private ?string $notes;

    private ?string $failure_reason;

    private ?mixed $ip_address;

    private ?string $user_agent;

    private ?array $location_data;

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
        $this->setTransactionId($data['transaction_id'] ?? null);
        $this->setReferenceId($data['reference_id'] ?? null);
        $this->setType($data['type'] ?? null);
        $this->setPaymentMethod($data['payment_method'] ?? null);
        $this->setPaymentGateway($data['payment_gateway'] ?? null);
        $this->setGatewayResponse($data['gateway_response'] ?? null);
        $this->setPaymentMethodDetails($data['payment_method_details'] ?? null);
        $this->setAmount($data['amount'] ?? 0);
        $this->setFee($data['fee'] ?? 0.00000000);
        $this->setTax($data['tax'] ?? 0.00000000);
        $this->setNetAmount($data['net_amount'] ?? null);
        $this->setCurrency($data['currency'] ?? null);
        $this->setExchangeRate($data['exchange_rate'] ?? 1.000000);
        $this->setStatus($data['status'] ?? null);
        $this->setCardLast4($data['card_last4'] ?? null);
        $this->setCardBrand($data['card_brand'] ?? null);
        $this->setCardCountry($data['card_country'] ?? null);
        $this->setCardExpMonth($data['card_exp_month'] ?? null);
        $this->setCardExpYear($data['card_exp_year'] ?? null);
        $this->setBankName($data['bank_name'] ?? null);
        $this->setBankAccountLast4($data['bank_account_last4'] ?? null);
        $this->setBankRoutingNumber($data['bank_routing_number'] ?? null);
        $this->setWalletType($data['wallet_type'] ?? null);
        $this->setWalletNumber($data['wallet_number'] ?? null);
        $this->setWalletTransactionId($data['wallet_transaction_id'] ?? null);
        $this->setInstallmentNumber($data['installment_number'] ?? null);
        $this->setTotalInstallments($data['total_installments'] ?? null);
        $this->setInitiatedAt($data['initiated_at'] ?? null);
        $this->setAuthorizedAt($data['authorized_at'] ?? null);
        $this->setCapturedAt($data['captured_at'] ?? null);
        $this->setCompletedAt($data['completed_at'] ?? null);
        $this->setFailedAt($data['failed_at'] ?? null);
        $this->setRefundedAt($data['refunded_at'] ?? null);
        $this->setFraudIndicators($data['fraud_indicators'] ?? null);
        $this->setRiskScore($data['risk_score'] ?? null);
        $this->setRequiresReview($data['requires_review'] ?? false);
        $this->setMetadata($data['metadata'] ?? null);
        $this->setCustomFields($data['custom_fields'] ?? null);
        $this->setNotes($data['notes'] ?? null);
        $this->setFailureReason($data['failure_reason'] ?? null);
        $this->setIpAddress($data['ip_address'] ?? null);
        $this->setUserAgent($data['user_agent'] ?? null);
        $this->setLocationData($data['location_data'] ?? null);
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

    public function setPaymentChildId(?mixed $value): void
    {
        $this->payment_child_id = $value;
    }

    public function getPaymentChildId(): ?mixed
    {
        return $this->payment_child_id;
    }

    public function setTransactionId(mixed $value): void
    {
        $this->transaction_id = $value;
    }

    public function getTransactionId(): mixed
    {
        return $this->transaction_id;
    }

    public function setReferenceId(?mixed $value): void
    {
        $this->reference_id = $value;
    }

    public function getReferenceId(): ?mixed
    {
        return $this->reference_id;
    }

    public function setType(mixed $value): void
    {
        $this->type = $value;
    }

    public function getType(): mixed
    {
        return $this->type;
    }

    public function setPaymentMethod(mixed $value): void
    {
        $this->payment_method = $value;
    }

    public function getPaymentMethod(): mixed
    {
        return $this->payment_method;
    }

    public function setPaymentGateway(mixed $value): void
    {
        $this->payment_gateway = $value;
    }

    public function getPaymentGateway(): mixed
    {
        return $this->payment_gateway;
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

    public function setTax(float $value): void
    {
        $this->tax = $value;
    }

    public function getTax(): float
    {
        return $this->tax;
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

    public function setStatus(mixed $value): void
    {
        $this->status = $value;
    }

    public function getStatus(): mixed
    {
        return $this->status;
    }

    public function setCardLast4(?mixed $value): void
    {
        $this->card_last4 = $value;
    }

    public function getCardLast4(): ?mixed
    {
        return $this->card_last4;
    }

    public function setCardBrand(?mixed $value): void
    {
        $this->card_brand = $value;
    }

    public function getCardBrand(): ?mixed
    {
        return $this->card_brand;
    }

    public function setCardCountry(?mixed $value): void
    {
        $this->card_country = $value;
    }

    public function getCardCountry(): ?mixed
    {
        return $this->card_country;
    }

    public function setCardExpMonth(?int $value): void
    {
        $this->card_exp_month = $value;
    }

    public function getCardExpMonth(): ?int
    {
        return $this->card_exp_month;
    }

    public function setCardExpYear(?int $value): void
    {
        $this->card_exp_year = $value;
    }

    public function getCardExpYear(): ?int
    {
        return $this->card_exp_year;
    }

    public function setBankName(?mixed $value): void
    {
        $this->bank_name = $value;
    }

    public function getBankName(): ?mixed
    {
        return $this->bank_name;
    }

    public function setBankAccountLast4(?mixed $value): void
    {
        $this->bank_account_last4 = $value;
    }

    public function getBankAccountLast4(): ?mixed
    {
        return $this->bank_account_last4;
    }

    public function setBankRoutingNumber(?mixed $value): void
    {
        $this->bank_routing_number = $value;
    }

    public function getBankRoutingNumber(): ?mixed
    {
        return $this->bank_routing_number;
    }

    public function setWalletType(?mixed $value): void
    {
        $this->wallet_type = $value;
    }

    public function getWalletType(): ?mixed
    {
        return $this->wallet_type;
    }

    public function setWalletNumber(?mixed $value): void
    {
        $this->wallet_number = $value;
    }

    public function getWalletNumber(): ?mixed
    {
        return $this->wallet_number;
    }

    public function setWalletTransactionId(?mixed $value): void
    {
        $this->wallet_transaction_id = $value;
    }

    public function getWalletTransactionId(): ?mixed
    {
        return $this->wallet_transaction_id;
    }

    public function setInstallmentNumber(?int $value): void
    {
        $this->installment_number = $value;
    }

    public function getInstallmentNumber(): ?int
    {
        return $this->installment_number;
    }

    public function setTotalInstallments(?int $value): void
    {
        $this->total_installments = $value;
    }

    public function getTotalInstallments(): ?int
    {
        return $this->total_installments;
    }

    public function setInitiatedAt(string $value): void
    {
        $this->initiated_at = $value;
    }

    public function getInitiatedAt(): ?string
    {
        if ($this->initiated_at instanceof \DateTimeInterface) {
            return $this->initiated_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->initiated_at)) {
            $timestamp = strtotime($this->initiated_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setAuthorizedAt(?string $value): void
    {
        $this->authorized_at = $value;
    }

    public function getAuthorizedAt(): ?string
    {
        if ($this->authorized_at instanceof \DateTimeInterface) {
            return $this->authorized_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->authorized_at)) {
            $timestamp = strtotime($this->authorized_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setCapturedAt(?string $value): void
    {
        $this->captured_at = $value;
    }

    public function getCapturedAt(): ?string
    {
        if ($this->captured_at instanceof \DateTimeInterface) {
            return $this->captured_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->captured_at)) {
            $timestamp = strtotime($this->captured_at);
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

    public function setRiskScore(?float $value): void
    {
        $this->risk_score = $value;
    }

    public function getRiskScore(): ?float
    {
        return $this->risk_score;
    }

    public function setRequiresReview(bool $value): void
    {
        $this->requires_review = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    public function getRequiresReview(): bool
    {
        return $this->requires_review ?? false;
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

    public function setLocationData(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->location_data = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->location_data = $value;
        }
    }

    public function getLocationData(): array
    {
        if (is_array($this->location_data)) {
            return $this->location_data;
        }

        if (is_string($this->location_data)) {
            $decoded = json_decode($this->location_data, true);

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
            'payment_child_id' => $this->getPaymentChildId(),
            'transaction_id' => $this->getTransactionId(),
            'reference_id' => $this->getReferenceId(),
            'type' => $this->getType(),
            'payment_method' => $this->getPaymentMethod(),
            'payment_gateway' => $this->getPaymentGateway(),
            'gateway_response' => $this->getGatewayResponse(),
            'payment_method_details' => $this->getPaymentMethodDetails(),
            'amount' => $this->getAmount(),
            'fee' => $this->getFee(),
            'tax' => $this->getTax(),
            'net_amount' => $this->getNetAmount(),
            'currency' => $this->getCurrency(),
            'exchange_rate' => $this->getExchangeRate(),
            'status' => $this->getStatus(),
            'card_last4' => $this->getCardLast4(),
            'card_brand' => $this->getCardBrand(),
            'card_country' => $this->getCardCountry(),
            'card_exp_month' => $this->getCardExpMonth(),
            'card_exp_year' => $this->getCardExpYear(),
            'bank_name' => $this->getBankName(),
            'bank_account_last4' => $this->getBankAccountLast4(),
            'bank_routing_number' => $this->getBankRoutingNumber(),
            'wallet_type' => $this->getWalletType(),
            'wallet_number' => $this->getWalletNumber(),
            'wallet_transaction_id' => $this->getWalletTransactionId(),
            'installment_number' => $this->getInstallmentNumber(),
            'total_installments' => $this->getTotalInstallments(),
            'initiated_at' => $this->getInitiatedAt(),
            'authorized_at' => $this->getAuthorizedAt(),
            'captured_at' => $this->getCapturedAt(),
            'completed_at' => $this->getCompletedAt(),
            'failed_at' => $this->getFailedAt(),
            'refunded_at' => $this->getRefundedAt(),
            'fraud_indicators' => $this->getFraudIndicators(),
            'risk_score' => $this->getRiskScore(),
            'requires_review' => $this->getRequiresReview(),
            'metadata' => $this->getMetadata(),
            'custom_fields' => $this->getCustomFields(),
            'notes' => $this->getNotes(),
            'failure_reason' => $this->getFailureReason(),
            'ip_address' => $this->getIpAddress(),
            'user_agent' => $this->getUserAgent(),
            'location_data' => $this->getLocationData(),
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

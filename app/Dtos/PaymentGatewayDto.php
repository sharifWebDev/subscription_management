<?php

namespace App\Dtos;

class PaymentGatewaysDto implements \JsonSerializable
{
    private mixed $id;

    private mixed $name;

    private mixed $code;

    private mixed $type;

    private bool $is_active;

    private bool $is_test_mode;

    private bool $supports_recurring;

    private bool $supports_refunds;

    private bool $supports_installments;

    private ?string $api_key;

    private ?string $api_secret;

    private ?string $webhook_secret;

    private ?string $merchant_id;

    private ?string $store_id;

    private ?string $store_password;

    private ?mixed $base_url;

    private ?mixed $callback_url;

    private ?mixed $webhook_url;

    private ?array $supported_currencies;

    private ?array $supported_countries;

    private ?array $excluded_countries;

    private float $percentage_fee;

    private float $fixed_fee;

    private mixed $fee_currency;

    private ?array $fee_structure;

    private ?array $config;

    private ?array $metadata;

    private int $settlement_days;

    private int $refund_days;

    private float $min_amount;

    private float $max_amount;

    private ?mixed $created_by;

    private ?mixed $updated_by;

    private string $created_at;

    private string $updated_at;

    private ?string $deleted_at;

    public function __construct(array $data = [])
    {
        $this->setId($data['id'] ?? null);
        $this->setName($data['name'] ?? null);
        $this->setCode($data['code'] ?? null);
        $this->setType($data['type'] ?? null);
        $this->setIsActive($data['is_active'] ?? false);
        $this->setIsTestMode($data['is_test_mode'] ?? true);
        $this->setSupportsRecurring($data['supports_recurring'] ?? false);
        $this->setSupportsRefunds($data['supports_refunds'] ?? false);
        $this->setSupportsInstallments($data['supports_installments'] ?? false);
        $this->setApiKey($data['api_key'] ?? null);
        $this->setApiSecret($data['api_secret'] ?? null);
        $this->setWebhookSecret($data['webhook_secret'] ?? null);
        $this->setMerchantId($data['merchant_id'] ?? null);
        $this->setStoreId($data['store_id'] ?? null);
        $this->setStorePassword($data['store_password'] ?? null);
        $this->setBaseUrl($data['base_url'] ?? null);
        $this->setCallbackUrl($data['callback_url'] ?? null);
        $this->setWebhookUrl($data['webhook_url'] ?? null);
        $this->setSupportedCurrencies($data['supported_currencies'] ?? null);
        $this->setSupportedCountries($data['supported_countries'] ?? null);
        $this->setExcludedCountries($data['excluded_countries'] ?? null);
        $this->setPercentageFee($data['percentage_fee'] ?? 0.00);
        $this->setFixedFee($data['fixed_fee'] ?? 0.00);
        $this->setFeeCurrency($data['fee_currency'] ?? null);
        $this->setFeeStructure($data['fee_structure'] ?? null);
        $this->setConfig($data['config'] ?? null);
        $this->setMetadata($data['metadata'] ?? null);
        $this->setSettlementDays($data['settlement_days'] ?? 2);
        $this->setRefundDays($data['refund_days'] ?? 5);
        $this->setMinAmount($data['min_amount'] ?? 0.00);
        $this->setMaxAmount($data['max_amount'] ?? 999999.00);
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

    public function setName(mixed $value): void
    {
        $this->name = $value;
    }

    public function getName(): mixed
    {
        return $this->name;
    }

    public function setCode(mixed $value): void
    {
        $this->code = $value;
    }

    public function getCode(): mixed
    {
        return $this->code;
    }

    public function setType(mixed $value): void
    {
        $this->type = $value;
    }

    public function getType(): mixed
    {
        return $this->type;
    }

    public function setIsActive(bool $value): void
    {
        $this->is_active = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    public function getIsActive(): bool
    {
        return $this->is_active ?? false;
    }

    public function setIsTestMode(bool $value): void
    {
        $this->is_test_mode = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    public function getIsTestMode(): bool
    {
        return $this->is_test_mode ?? false;
    }

    public function setSupportsRecurring(bool $value): void
    {
        $this->supports_recurring = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    public function getSupportsRecurring(): bool
    {
        return $this->supports_recurring ?? false;
    }

    public function setSupportsRefunds(bool $value): void
    {
        $this->supports_refunds = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    public function getSupportsRefunds(): bool
    {
        return $this->supports_refunds ?? false;
    }

    public function setSupportsInstallments(bool $value): void
    {
        $this->supports_installments = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    public function getSupportsInstallments(): bool
    {
        return $this->supports_installments ?? false;
    }

    public function setApiKey(?string $value): void
    {
        $this->api_key = $value;
    }

    public function getApiKey(): ?string
    {
        return $this->api_key;
    }

    public function setApiSecret(?string $value): void
    {
        $this->api_secret = $value;
    }

    public function getApiSecret(): ?string
    {
        return $this->api_secret;
    }

    public function setWebhookSecret(?string $value): void
    {
        $this->webhook_secret = $value;
    }

    public function getWebhookSecret(): ?string
    {
        return $this->webhook_secret;
    }

    public function setMerchantId(?string $value): void
    {
        $this->merchant_id = $value;
    }

    public function getMerchantId(): ?string
    {
        return $this->merchant_id;
    }

    public function setStoreId(?string $value): void
    {
        $this->store_id = $value;
    }

    public function getStoreId(): ?string
    {
        return $this->store_id;
    }

    public function setStorePassword(?string $value): void
    {
        $this->store_password = $value;
    }

    public function getStorePassword(): ?string
    {
        return $this->store_password;
    }

    public function setBaseUrl(?mixed $value): void
    {
        $this->base_url = $value;
    }

    public function getBaseUrl(): ?mixed
    {
        return $this->base_url;
    }

    public function setCallbackUrl(?mixed $value): void
    {
        $this->callback_url = $value;
    }

    public function getCallbackUrl(): ?mixed
    {
        return $this->callback_url;
    }

    public function setWebhookUrl(?mixed $value): void
    {
        $this->webhook_url = $value;
    }

    public function getWebhookUrl(): ?mixed
    {
        return $this->webhook_url;
    }

    public function setSupportedCurrencies(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->supported_currencies = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->supported_currencies = $value;
        }
    }

    public function getSupportedCurrencies(): array
    {
        if (is_array($this->supported_currencies)) {
            return $this->supported_currencies;
        }

        if (is_string($this->supported_currencies)) {
            $decoded = json_decode($this->supported_currencies, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
    }

    public function setSupportedCountries(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->supported_countries = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->supported_countries = $value;
        }
    }

    public function getSupportedCountries(): array
    {
        if (is_array($this->supported_countries)) {
            return $this->supported_countries;
        }

        if (is_string($this->supported_countries)) {
            $decoded = json_decode($this->supported_countries, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
    }

    public function setExcludedCountries(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->excluded_countries = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->excluded_countries = $value;
        }
    }

    public function getExcludedCountries(): array
    {
        if (is_array($this->excluded_countries)) {
            return $this->excluded_countries;
        }

        if (is_string($this->excluded_countries)) {
            $decoded = json_decode($this->excluded_countries, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
    }

    public function setPercentageFee(float $value): void
    {
        $this->percentage_fee = $value;
    }

    public function getPercentageFee(): float
    {
        return $this->percentage_fee;
    }

    public function setFixedFee(float $value): void
    {
        $this->fixed_fee = $value;
    }

    public function getFixedFee(): float
    {
        return $this->fixed_fee;
    }

    public function setFeeCurrency(mixed $value): void
    {
        $this->fee_currency = $value;
    }

    public function getFeeCurrency(): mixed
    {
        return $this->fee_currency;
    }

    public function setFeeStructure(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->fee_structure = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->fee_structure = $value;
        }
    }

    public function getFeeStructure(): array
    {
        if (is_array($this->fee_structure)) {
            return $this->fee_structure;
        }

        if (is_string($this->fee_structure)) {
            $decoded = json_decode($this->fee_structure, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
    }

    public function setConfig(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->config = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->config = $value;
        }
    }

    public function getConfig(): array
    {
        if (is_array($this->config)) {
            return $this->config;
        }

        if (is_string($this->config)) {
            $decoded = json_decode($this->config, true);

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

    public function setSettlementDays(int $value): void
    {
        $this->settlement_days = $value;
    }

    public function getSettlementDays(): int
    {
        return $this->settlement_days;
    }

    public function setRefundDays(int $value): void
    {
        $this->refund_days = $value;
    }

    public function getRefundDays(): int
    {
        return $this->refund_days;
    }

    public function setMinAmount(float $value): void
    {
        $this->min_amount = $value;
    }

    public function getMinAmount(): float
    {
        return $this->min_amount;
    }

    public function setMaxAmount(float $value): void
    {
        $this->max_amount = $value;
    }

    public function getMaxAmount(): float
    {
        return $this->max_amount;
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
            'name' => $this->getName(),
            'code' => $this->getCode(),
            'type' => $this->getType(),
            'is_active' => $this->getIsActive(),
            'is_test_mode' => $this->getIsTestMode(),
            'supports_recurring' => $this->getSupportsRecurring(),
            'supports_refunds' => $this->getSupportsRefunds(),
            'supports_installments' => $this->getSupportsInstallments(),
            'api_key' => $this->getApiKey(),
            'api_secret' => $this->getApiSecret(),
            'webhook_secret' => $this->getWebhookSecret(),
            'merchant_id' => $this->getMerchantId(),
            'store_id' => $this->getStoreId(),
            'store_password' => $this->getStorePassword(),
            'base_url' => $this->getBaseUrl(),
            'callback_url' => $this->getCallbackUrl(),
            'webhook_url' => $this->getWebhookUrl(),
            'supported_currencies' => $this->getSupportedCurrencies(),
            'supported_countries' => $this->getSupportedCountries(),
            'excluded_countries' => $this->getExcludedCountries(),
            'percentage_fee' => $this->getPercentageFee(),
            'fixed_fee' => $this->getFixedFee(),
            'fee_currency' => $this->getFeeCurrency(),
            'fee_structure' => $this->getFeeStructure(),
            'config' => $this->getConfig(),
            'metadata' => $this->getMetadata(),
            'settlement_days' => $this->getSettlementDays(),
            'refund_days' => $this->getRefundDays(),
            'min_amount' => $this->getMinAmount(),
            'max_amount' => $this->getMaxAmount(),
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

<?php

namespace App\Dtos;

class PaymentMethodsDto implements \JsonSerializable
{
    private mixed $id;

    private mixed $user_id;

    private mixed $type;

    private mixed $gateway;

    private ?mixed $gateway_customer_id;

    private ?mixed $gateway_payment_method_id;

    private ?mixed $nickname;

    private bool $is_default;

    private bool $is_verified;

    private ?mixed $card_last4;

    private ?mixed $card_brand;

    private ?int $card_exp_month;

    private ?int $card_exp_year;

    private ?mixed $card_country;

    private ?mixed $bank_name;

    private ?mixed $bank_account_last4;

    private ?mixed $bank_account_type;

    private ?mixed $bank_routing_number;

    private ?mixed $wallet_type;

    private ?mixed $wallet_number;

    private ?mixed $crypto_currency;

    private ?mixed $crypto_address;

    private ?array $encrypted_data;

    private ?mixed $fingerprint;

    private bool $is_compromised;

    private ?array $metadata;

    private ?array $gateway_metadata;

    private ?string $verified_at;

    private ?mixed $verified_by;

    private ?string $last_used_at;

    private int $usage_count;

    private ?mixed $created_by;

    private ?mixed $updated_by;

    private string $created_at;

    private string $updated_at;

    private ?string $deleted_at;

    public function __construct(array $data = [])
    {
        $this->setId($data['id'] ?? null);
        $this->setUserId($data['user_id'] ?? null);
        $this->setType($data['type'] ?? null);
        $this->setGateway($data['gateway'] ?? null);
        $this->setGatewayCustomerId($data['gateway_customer_id'] ?? null);
        $this->setGatewayPaymentMethodId($data['gateway_payment_method_id'] ?? null);
        $this->setNickname($data['nickname'] ?? null);
        $this->setIsDefault($data['is_default'] ?? false);
        $this->setIsVerified($data['is_verified'] ?? false);
        $this->setCardLast4($data['card_last4'] ?? null);
        $this->setCardBrand($data['card_brand'] ?? null);
        $this->setCardExpMonth($data['card_exp_month'] ?? null);
        $this->setCardExpYear($data['card_exp_year'] ?? null);
        $this->setCardCountry($data['card_country'] ?? null);
        $this->setBankName($data['bank_name'] ?? null);
        $this->setBankAccountLast4($data['bank_account_last4'] ?? null);
        $this->setBankAccountType($data['bank_account_type'] ?? null);
        $this->setBankRoutingNumber($data['bank_routing_number'] ?? null);
        $this->setWalletType($data['wallet_type'] ?? null);
        $this->setWalletNumber($data['wallet_number'] ?? null);
        $this->setCryptoCurrency($data['crypto_currency'] ?? null);
        $this->setCryptoAddress($data['crypto_address'] ?? null);
        $this->setEncryptedData($data['encrypted_data'] ?? null);
        $this->setFingerprint($data['fingerprint'] ?? null);
        $this->setIsCompromised($data['is_compromised'] ?? false);
        $this->setMetadata($data['metadata'] ?? null);
        $this->setGatewayMetadata($data['gateway_metadata'] ?? null);
        $this->setVerifiedAt($data['verified_at'] ?? null);
        $this->setVerifiedBy($data['verified_by'] ?? null);
        $this->setLastUsedAt($data['last_used_at'] ?? null);
        $this->setUsageCount($data['usage_count'] ?? 0);
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

    public function setType(mixed $value): void
    {
        $this->type = $value;
    }

    public function getType(): mixed
    {
        return $this->type;
    }

    public function setGateway(mixed $value): void
    {
        $this->gateway = $value;
    }

    public function getGateway(): mixed
    {
        return $this->gateway;
    }

    public function setGatewayCustomerId(?mixed $value): void
    {
        $this->gateway_customer_id = $value;
    }

    public function getGatewayCustomerId(): ?mixed
    {
        return $this->gateway_customer_id;
    }

    public function setGatewayPaymentMethodId(?mixed $value): void
    {
        $this->gateway_payment_method_id = $value;
    }

    public function getGatewayPaymentMethodId(): ?mixed
    {
        return $this->gateway_payment_method_id;
    }

    public function setNickname(?mixed $value): void
    {
        $this->nickname = $value;
    }

    public function getNickname(): ?mixed
    {
        return $this->nickname;
    }

    public function setIsDefault(bool $value): void
    {
        $this->is_default = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    public function getIsDefault(): bool
    {
        return $this->is_default ?? false;
    }

    public function setIsVerified(bool $value): void
    {
        $this->is_verified = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    public function getIsVerified(): bool
    {
        return $this->is_verified ?? false;
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

    public function setCardCountry(?mixed $value): void
    {
        $this->card_country = $value;
    }

    public function getCardCountry(): ?mixed
    {
        return $this->card_country;
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

    public function setBankAccountType(?mixed $value): void
    {
        $this->bank_account_type = $value;
    }

    public function getBankAccountType(): ?mixed
    {
        return $this->bank_account_type;
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

    public function setCryptoCurrency(?mixed $value): void
    {
        $this->crypto_currency = $value;
    }

    public function getCryptoCurrency(): ?mixed
    {
        return $this->crypto_currency;
    }

    public function setCryptoAddress(?mixed $value): void
    {
        $this->crypto_address = $value;
    }

    public function getCryptoAddress(): ?mixed
    {
        return $this->crypto_address;
    }

    public function setEncryptedData(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->encrypted_data = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->encrypted_data = $value;
        }
    }

    public function getEncryptedData(): array
    {
        if (is_array($this->encrypted_data)) {
            return $this->encrypted_data;
        }

        if (is_string($this->encrypted_data)) {
            $decoded = json_decode($this->encrypted_data, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
    }

    public function setFingerprint(?mixed $value): void
    {
        $this->fingerprint = $value;
    }

    public function getFingerprint(): ?mixed
    {
        return $this->fingerprint;
    }

    public function setIsCompromised(bool $value): void
    {
        $this->is_compromised = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    public function getIsCompromised(): bool
    {
        return $this->is_compromised ?? false;
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

    public function setGatewayMetadata(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->gateway_metadata = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->gateway_metadata = $value;
        }
    }

    public function getGatewayMetadata(): array
    {
        if (is_array($this->gateway_metadata)) {
            return $this->gateway_metadata;
        }

        if (is_string($this->gateway_metadata)) {
            $decoded = json_decode($this->gateway_metadata, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
    }

    public function setVerifiedAt(?string $value): void
    {
        $this->verified_at = $value;
    }

    public function getVerifiedAt(): ?string
    {
        if ($this->verified_at instanceof \DateTimeInterface) {
            return $this->verified_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->verified_at)) {
            $timestamp = strtotime($this->verified_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setVerifiedBy(?mixed $value): void
    {
        $this->verified_by = $value;
    }

    public function getVerifiedBy(): ?mixed
    {
        return $this->verified_by;
    }

    public function setLastUsedAt(?string $value): void
    {
        $this->last_used_at = $value;
    }

    public function getLastUsedAt(): ?string
    {
        if ($this->last_used_at instanceof \DateTimeInterface) {
            return $this->last_used_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->last_used_at)) {
            $timestamp = strtotime($this->last_used_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setUsageCount(int $value): void
    {
        $this->usage_count = $value;
    }

    public function getUsageCount(): int
    {
        return $this->usage_count;
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
            'type' => $this->getType(),
            'gateway' => $this->getGateway(),
            'gateway_customer_id' => $this->getGatewayCustomerId(),
            'gateway_payment_method_id' => $this->getGatewayPaymentMethodId(),
            'nickname' => $this->getNickname(),
            'is_default' => $this->getIsDefault(),
            'is_verified' => $this->getIsVerified(),
            'card_last4' => $this->getCardLast4(),
            'card_brand' => $this->getCardBrand(),
            'card_exp_month' => $this->getCardExpMonth(),
            'card_exp_year' => $this->getCardExpYear(),
            'card_country' => $this->getCardCountry(),
            'bank_name' => $this->getBankName(),
            'bank_account_last4' => $this->getBankAccountLast4(),
            'bank_account_type' => $this->getBankAccountType(),
            'bank_routing_number' => $this->getBankRoutingNumber(),
            'wallet_type' => $this->getWalletType(),
            'wallet_number' => $this->getWalletNumber(),
            'crypto_currency' => $this->getCryptoCurrency(),
            'crypto_address' => $this->getCryptoAddress(),
            'encrypted_data' => $this->getEncryptedData(),
            'fingerprint' => $this->getFingerprint(),
            'is_compromised' => $this->getIsCompromised(),
            'metadata' => $this->getMetadata(),
            'gateway_metadata' => $this->getGatewayMetadata(),
            'verified_at' => $this->getVerifiedAt(),
            'verified_by' => $this->getVerifiedBy(),
            'last_used_at' => $this->getLastUsedAt(),
            'usage_count' => $this->getUsageCount(),
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

<?php

namespace App\Dtos;

class SubscriptionsDto implements \JsonSerializable
{
    private mixed $id;

    private mixed $user_id;

    private mixed $plan_id;

    private mixed $plan_price_id;

    private ?mixed $parent_subscription_id;

    private mixed $status;

    private mixed $billing_cycle_anchor;

    private int $quantity;

    private float $unit_price;

    private float $amount;

    private mixed $currency;

    private ?string $trial_starts_at;

    private ?string $trial_ends_at;

    private bool $trial_converted;

    private ?string $current_period_starts_at;

    private ?string $current_period_ends_at;

    private ?string $billing_cycle_anchor_date;

    private ?string $canceled_at;

    private ?mixed $cancellation_reason;

    private bool $prorate;

    private ?float $proration_amount;

    private ?string $proration_date;

    private mixed $gateway;

    private ?mixed $gateway_subscription_id;

    private ?mixed $gateway_customer_id;

    private ?array $gateway_metadata;

    private ?array $metadata;

    private ?array $history;

    private ?mixed $created_by;

    private ?mixed $updated_by;

    private string $created_at;

    private string $updated_at;

    private ?string $deleted_at;

    private bool $is_active;

    public function __construct(array $data = [])
    {
        $this->setId($data['id'] ?? null);
        $this->setUserId($data['user_id'] ?? null);
        $this->setPlanId($data['plan_id'] ?? null);
        $this->setPlanPriceId($data['plan_price_id'] ?? null);
        $this->setParentSubscriptionId($data['parent_subscription_id'] ?? null);
        $this->setStatus($data['status'] ?? null);
        $this->setBillingCycleAnchor($data['billing_cycle_anchor'] ?? null);
        $this->setQuantity($data['quantity'] ?? 1);
        $this->setUnitPrice($data['unit_price'] ?? 0);
        $this->setAmount($data['amount'] ?? 0);
        $this->setCurrency($data['currency'] ?? null);
        $this->setTrialStartsAt($data['trial_starts_at'] ?? null);
        $this->setTrialEndsAt($data['trial_ends_at'] ?? null);
        $this->setTrialConverted($data['trial_converted'] ?? false);
        $this->setCurrentPeriodStartsAt($data['current_period_starts_at'] ?? null);
        $this->setCurrentPeriodEndsAt($data['current_period_ends_at'] ?? null);
        $this->setBillingCycleAnchorDate($data['billing_cycle_anchor_date'] ?? null);
        $this->setCanceledAt($data['canceled_at'] ?? null);
        $this->setCancellationReason($data['cancellation_reason'] ?? null);
        $this->setProrate($data['prorate'] ?? true);
        $this->setProrationAmount($data['proration_amount'] ?? null);
        $this->setProrationDate($data['proration_date'] ?? null);
        $this->setGateway($data['gateway'] ?? null);
        $this->setGatewaySubscriptionId($data['gateway_subscription_id'] ?? null);
        $this->setGatewayCustomerId($data['gateway_customer_id'] ?? null);
        $this->setGatewayMetadata($data['gateway_metadata'] ?? null);
        $this->setMetadata($data['metadata'] ?? null);
        $this->setHistory($data['history'] ?? null);
        $this->setCreatedBy($data['created_by'] ?? null);
        $this->setUpdatedBy($data['updated_by'] ?? null);
        $this->setCreatedAt($data['created_at'] ?? null);
        $this->setUpdatedAt($data['updated_at'] ?? null);
        $this->setDeletedAt($data['deleted_at'] ?? null);
        $this->setIsActive($data['is_active'] ?? false);
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

    public function setPlanId(mixed $value): void
    {
        $this->plan_id = $value;
    }

    public function getPlanId(): mixed
    {
        return $this->plan_id;
    }

    public function setPlanPriceId(mixed $value): void
    {
        $this->plan_price_id = $value;
    }

    public function getPlanPriceId(): mixed
    {
        return $this->plan_price_id;
    }

    public function setParentSubscriptionId(?mixed $value): void
    {
        $this->parent_subscription_id = $value;
    }

    public function getParentSubscriptionId(): ?mixed
    {
        return $this->parent_subscription_id;
    }

    public function setStatus(mixed $value): void
    {
        $this->status = $value;
    }

    public function getStatus(): mixed
    {
        return $this->status;
    }

    public function setBillingCycleAnchor(mixed $value): void
    {
        $this->billing_cycle_anchor = $value;
    }

    public function getBillingCycleAnchor(): mixed
    {
        return $this->billing_cycle_anchor;
    }

    public function setQuantity(int $value): void
    {
        $this->quantity = $value;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
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

    public function setCurrency(mixed $value): void
    {
        $this->currency = $value;
    }

    public function getCurrency(): mixed
    {
        return $this->currency;
    }

    public function setTrialStartsAt(?string $value): void
    {
        $this->trial_starts_at = $value;
    }

    public function getTrialStartsAt(): ?string
    {
        if ($this->trial_starts_at instanceof \DateTimeInterface) {
            return $this->trial_starts_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->trial_starts_at)) {
            $timestamp = strtotime($this->trial_starts_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setTrialEndsAt(?string $value): void
    {
        $this->trial_ends_at = $value;
    }

    public function getTrialEndsAt(): ?string
    {
        if ($this->trial_ends_at instanceof \DateTimeInterface) {
            return $this->trial_ends_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->trial_ends_at)) {
            $timestamp = strtotime($this->trial_ends_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setTrialConverted(bool $value): void
    {
        $this->trial_converted = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    public function getTrialConverted(): bool
    {
        return $this->trial_converted ?? false;
    }

    public function setCurrentPeriodStartsAt(?string $value): void
    {
        $this->current_period_starts_at = $value;
    }

    public function getCurrentPeriodStartsAt(): ?string
    {
        if ($this->current_period_starts_at instanceof \DateTimeInterface) {
            return $this->current_period_starts_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->current_period_starts_at)) {
            $timestamp = strtotime($this->current_period_starts_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setCurrentPeriodEndsAt(?string $value): void
    {
        $this->current_period_ends_at = $value;
    }

    public function getCurrentPeriodEndsAt(): ?string
    {
        if ($this->current_period_ends_at instanceof \DateTimeInterface) {
            return $this->current_period_ends_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->current_period_ends_at)) {
            $timestamp = strtotime($this->current_period_ends_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setBillingCycleAnchorDate(?string $value): void
    {
        $this->billing_cycle_anchor_date = $value;
    }

    public function getBillingCycleAnchorDate(): ?string
    {
        if ($this->billing_cycle_anchor_date instanceof \DateTimeInterface) {
            return $this->billing_cycle_anchor_date->format('Y-m-d H:i:s');
        }

        if (is_string($this->billing_cycle_anchor_date)) {
            $timestamp = strtotime($this->billing_cycle_anchor_date);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setCanceledAt(?string $value): void
    {
        $this->canceled_at = $value;
    }

    public function getCanceledAt(): ?string
    {
        if ($this->canceled_at instanceof \DateTimeInterface) {
            return $this->canceled_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->canceled_at)) {
            $timestamp = strtotime($this->canceled_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setCancellationReason(?mixed $value): void
    {
        $this->cancellation_reason = $value;
    }

    public function getCancellationReason(): ?mixed
    {
        return $this->cancellation_reason;
    }

    public function setProrate(bool $value): void
    {
        $this->prorate = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    public function getProrate(): bool
    {
        return $this->prorate ?? false;
    }

    public function setProrationAmount(?float $value): void
    {
        $this->proration_amount = $value;
    }

    public function getProrationAmount(): ?float
    {
        return $this->proration_amount;
    }

    public function setProrationDate(?string $value): void
    {
        $this->proration_date = $value;
    }

    public function getProrationDate(): ?string
    {
        if ($this->proration_date instanceof \DateTimeInterface) {
            return $this->proration_date->format('Y-m-d H:i:s');
        }

        if (is_string($this->proration_date)) {
            $timestamp = strtotime($this->proration_date);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setGateway(mixed $value): void
    {
        $this->gateway = $value;
    }

    public function getGateway(): mixed
    {
        return $this->gateway;
    }

    public function setGatewaySubscriptionId(?mixed $value): void
    {
        $this->gateway_subscription_id = $value;
    }

    public function getGatewaySubscriptionId(): ?mixed
    {
        return $this->gateway_subscription_id;
    }

    public function setGatewayCustomerId(?mixed $value): void
    {
        $this->gateway_customer_id = $value;
    }

    public function getGatewayCustomerId(): ?mixed
    {
        return $this->gateway_customer_id;
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

    public function setHistory(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->history = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->history = $value;
        }
    }

    public function getHistory(): array
    {
        if (is_array($this->history)) {
            return $this->history;
        }

        if (is_string($this->history)) {
            $decoded = json_decode($this->history, true);

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

    public function setIsActive(bool $value): void
    {
        $this->is_active = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    public function getIsActive(): bool
    {
        return $this->is_active ?? false;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'user_id' => $this->getUserId(),
            'plan_id' => $this->getPlanId(),
            'plan_price_id' => $this->getPlanPriceId(),
            'parent_subscription_id' => $this->getParentSubscriptionId(),
            'status' => $this->getStatus(),
            'billing_cycle_anchor' => $this->getBillingCycleAnchor(),
            'quantity' => $this->getQuantity(),
            'unit_price' => $this->getUnitPrice(),
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'trial_starts_at' => $this->getTrialStartsAt(),
            'trial_ends_at' => $this->getTrialEndsAt(),
            'trial_converted' => $this->getTrialConverted(),
            'current_period_starts_at' => $this->getCurrentPeriodStartsAt(),
            'current_period_ends_at' => $this->getCurrentPeriodEndsAt(),
            'billing_cycle_anchor_date' => $this->getBillingCycleAnchorDate(),
            'canceled_at' => $this->getCanceledAt(),
            'cancellation_reason' => $this->getCancellationReason(),
            'prorate' => $this->getProrate(),
            'proration_amount' => $this->getProrationAmount(),
            'proration_date' => $this->getProrationDate(),
            'gateway' => $this->getGateway(),
            'gateway_subscription_id' => $this->getGatewaySubscriptionId(),
            'gateway_customer_id' => $this->getGatewayCustomerId(),
            'gateway_metadata' => $this->getGatewayMetadata(),
            'metadata' => $this->getMetadata(),
            'history' => $this->getHistory(),
            'created_by' => $this->getCreatedBy(),
            'updated_by' => $this->getUpdatedBy(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
            'deleted_at' => $this->getDeletedAt(),
            'is_active' => $this->getIsActive(),
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

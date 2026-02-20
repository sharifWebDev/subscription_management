<?php

namespace App\Dtos;

class InvoicesDto implements \JsonSerializable
{
    private mixed $id;

    private mixed $user_id;

    private ?mixed $subscription_id;

    private mixed $number;

    private ?mixed $external_id;

    private mixed $type;

    private mixed $status;

    private float $subtotal;

    private float $tax;

    private float $total;

    private float $amount_due;

    private float $amount_paid;

    private ?float $amount_remaining;

    private mixed $currency;

    private string $issue_date;

    private ?string $due_date;

    private ?string $paid_at;

    private ?string $finalized_at;

    private ?array $line_items;

    private ?array $tax_rates;

    private ?array $discounts;

    private ?array $metadata;

    private ?array $history;

    private ?mixed $pdf_url;

    private ?mixed $created_by;

    private ?mixed $updated_by;

    private string $created_at;

    private string $updated_at;

    private ?string $deleted_at;

    public function __construct(array $data = [])
    {
        $this->setId($data['id'] ?? null);
        $this->setUserId($data['user_id'] ?? null);
        $this->setSubscriptionId($data['subscription_id'] ?? null);
        $this->setNumber($data['number'] ?? null);
        $this->setExternalId($data['external_id'] ?? null);
        $this->setType($data['type'] ?? null);
        $this->setStatus($data['status'] ?? null);
        $this->setSubtotal($data['subtotal'] ?? 0);
        $this->setTax($data['tax'] ?? 0.00000000);
        $this->setTotal($data['total'] ?? 0);
        $this->setAmountDue($data['amount_due'] ?? 0);
        $this->setAmountPaid($data['amount_paid'] ?? 0.00000000);
        $this->setAmountRemaining($data['amount_remaining'] ?? null);
        $this->setCurrency($data['currency'] ?? null);
        $this->setIssueDate($data['issue_date'] ?? null);
        $this->setDueDate($data['due_date'] ?? null);
        $this->setPaidAt($data['paid_at'] ?? null);
        $this->setFinalizedAt($data['finalized_at'] ?? null);
        $this->setLineItems($data['line_items'] ?? null);
        $this->setTaxRates($data['tax_rates'] ?? null);
        $this->setDiscounts($data['discounts'] ?? null);
        $this->setMetadata($data['metadata'] ?? null);
        $this->setHistory($data['history'] ?? null);
        $this->setPdfUrl($data['pdf_url'] ?? null);
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

    public function setSubscriptionId(?mixed $value): void
    {
        $this->subscription_id = $value;
    }

    public function getSubscriptionId(): ?mixed
    {
        return $this->subscription_id;
    }

    public function setNumber(mixed $value): void
    {
        $this->number = $value;
    }

    public function getNumber(): mixed
    {
        return $this->number;
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

    public function setSubtotal(float $value): void
    {
        $this->subtotal = $value;
    }

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function setTax(float $value): void
    {
        $this->tax = $value;
    }

    public function getTax(): float
    {
        return $this->tax;
    }

    public function setTotal(float $value): void
    {
        $this->total = $value;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setAmountDue(float $value): void
    {
        $this->amount_due = $value;
    }

    public function getAmountDue(): float
    {
        return $this->amount_due;
    }

    public function setAmountPaid(float $value): void
    {
        $this->amount_paid = $value;
    }

    public function getAmountPaid(): float
    {
        return $this->amount_paid;
    }

    public function setAmountRemaining(?float $value): void
    {
        $this->amount_remaining = $value;
    }

    public function getAmountRemaining(): ?float
    {
        return $this->amount_remaining;
    }

    public function setCurrency(mixed $value): void
    {
        $this->currency = $value;
    }

    public function getCurrency(): mixed
    {
        return $this->currency;
    }

    public function setIssueDate(string $value): void
    {
        $this->issue_date = $value;
    }

    public function getIssueDate(): ?string
    {
        if ($this->issue_date instanceof \DateTimeInterface) {
            return $this->issue_date->format('Y-m-d H:i:s');
        }

        if (is_string($this->issue_date)) {
            $timestamp = strtotime($this->issue_date);
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

    public function setFinalizedAt(?string $value): void
    {
        $this->finalized_at = $value;
    }

    public function getFinalizedAt(): ?string
    {
        if ($this->finalized_at instanceof \DateTimeInterface) {
            return $this->finalized_at->format('Y-m-d H:i:s');
        }

        if (is_string($this->finalized_at)) {
            $timestamp = strtotime($this->finalized_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setLineItems(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->line_items = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->line_items = $value;
        }
    }

    public function getLineItems(): array
    {
        if (is_array($this->line_items)) {
            return $this->line_items;
        }

        if (is_string($this->line_items)) {
            $decoded = json_decode($this->line_items, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
    }

    public function setTaxRates(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->tax_rates = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->tax_rates = $value;
        }
    }

    public function getTaxRates(): array
    {
        if (is_array($this->tax_rates)) {
            return $this->tax_rates;
        }

        if (is_string($this->tax_rates)) {
            $decoded = json_decode($this->tax_rates, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        return [];
    }

    public function setDiscounts(?array $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->discounts = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        } else {
            $this->discounts = $value;
        }
    }

    public function getDiscounts(): array
    {
        if (is_array($this->discounts)) {
            return $this->discounts;
        }

        if (is_string($this->discounts)) {
            $decoded = json_decode($this->discounts, true);

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

    public function setPdfUrl(?mixed $value): void
    {
        $this->pdf_url = $value;
    }

    public function getPdfUrl(): ?mixed
    {
        return $this->pdf_url;
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
            'subscription_id' => $this->getSubscriptionId(),
            'number' => $this->getNumber(),
            'external_id' => $this->getExternalId(),
            'type' => $this->getType(),
            'status' => $this->getStatus(),
            'subtotal' => $this->getSubtotal(),
            'tax' => $this->getTax(),
            'total' => $this->getTotal(),
            'amount_due' => $this->getAmountDue(),
            'amount_paid' => $this->getAmountPaid(),
            'amount_remaining' => $this->getAmountRemaining(),
            'currency' => $this->getCurrency(),
            'issue_date' => $this->getIssueDate(),
            'due_date' => $this->getDueDate(),
            'paid_at' => $this->getPaidAt(),
            'finalized_at' => $this->getFinalizedAt(),
            'line_items' => $this->getLineItems(),
            'tax_rates' => $this->getTaxRates(),
            'discounts' => $this->getDiscounts(),
            'metadata' => $this->getMetadata(),
            'history' => $this->getHistory(),
            'pdf_url' => $this->getPdfUrl(),
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

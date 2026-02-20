<?php

namespace App\Dtos;

class HkProdUomsDto implements \JsonSerializable
{
    private int $id;

    private mixed $code;

    private mixed $name;

    private bool $is_active;

    private ?int $sequence;

    private int $created_by;

    private ?int $updated_by;

    private string $created_at;

    private ?string $updated_at;

    public function __construct(array $data = [])
    {
        $this->setId($data['id'] ?? null);
        $this->setCode($data['code'] ?? null);
        $this->setName($data['name'] ?? null);
        $this->setIsActive($data['is_active'] ?? false);
        $this->setSequence($data['sequence'] ?? null);
        $this->setCreatedBy($data['created_by'] ?? 0);
        $this->setUpdatedBy($data['updated_by'] ?? null);
        $this->setCreatedAt($data['created_at'] ?? null);
        $this->setUpdatedAt($data['updated_at'] ?? null);
    }

    public function setId(int $value): void
    {
        $this->id = $value;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setCode(mixed $value): void
    {
        $this->code = $value;
    }

    public function getCode(): mixed
    {
        return $this->code;
    }

    public function setName(mixed $value): void
    {
        $this->name = $value;
    }

    public function getName(): mixed
    {
        return $this->name;
    }

    public function setIsActive(bool $value): void
    {
        $this->is_active = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    public function getIsActive(): bool
    {
        return $this->is_active ?? false;
    }

    public function setSequence(?int $value): void
    {
        $this->sequence = $value;
    }

    public function getSequence(): ?int
    {
        return $this->sequence;
    }

    public function setCreatedBy(int $value): void
    {
        $this->created_by = $value;
    }

    public function getCreatedBy(): int
    {
        return $this->created_by;
    }

    public function setUpdatedBy(?int $value): void
    {
        $this->updated_by = $value;
    }

    public function getUpdatedBy(): ?int
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

    public function setUpdatedAt(?string $value): void
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

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'code' => $this->getCode(),
            'name' => $this->getName(),
            'is_active' => $this->getIsActive(),
            'sequence' => $this->getSequence(),
            'created_by' => $this->getCreatedBy(),
            'updated_by' => $this->getUpdatedBy(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
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

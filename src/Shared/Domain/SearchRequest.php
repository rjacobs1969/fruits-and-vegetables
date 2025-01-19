<?php

declare(strict_types=1);

namespace App\Shared\Domain;

use App\Produce\Domain\ValueObject\ProduceType;
use App\Shared\Domain\WeightUnit;

class SearchRequest
{
    public function __construct(
        private ?int $id,
        private ?string $name,
        private ?ProduceType $type,
        private ?WeightUnit $weightUnit
    ) {}

    public function id(): ?int
    {
        return $this->id;
    }

    public function name(): ?string
    {
        return $this->name;
    }

    public function type(): ?ProduceType
    {
        return $this->type;
    }

    public function weightUnit(): WeightUnit
    {
        return $this->weightUnit;
    }

    public function hasFilters(): bool
    {
        return !empty($this->name) || $this->type === null;
    }

    public function getFilters(): array
    {
        $filters = [];

        if ($this->id !== null) {
            $filters['id'] = $this->id;
        }

        if ($this->type !== null) {
            $filters['type'] = (string) $this->type->value;
        }

        if (!empty($this->name)) {
            $filters['name'] = $this->name;
        }

        return $filters;
    }
}
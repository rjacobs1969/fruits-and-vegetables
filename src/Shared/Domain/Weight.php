<?php

declare(strict_types=1);

namespace App\Shared\Domain;

class Weight
{
    public function __construct(private int $value, private WeightUnit $unit)
    {
        $this->validate($this->value);
    }

    public function value(): int
    {
        return $this->value;
    }

    public function unit(): WeightUnit
    {
        return $this->unit;
    }

    public function toUnit(WeightUnit $destinationUnit): self
    {
        $conversionRate = $this->unit->getConversionRate($destinationUnit);
        $newValue = $this->value * $conversionRate;

        return new self((int) $newValue, $destinationUnit);
    }

    private function validate(int $value): void
    {
        if ($value < 0) {
            throw new WeightException('Weight cannot be negative');
        }
    }
}
<?php

declare(strict_types=1);

namespace App\Shared\Domain;

class Weight
{
    public function __construct(private float $value, private WeightUnit $unit)
    {
        $this->validate($this->value);
    }

    public function value(): float
    {
        return $this->value;
    }

    public function unit(): WeightUnit
    {
        return $this->unit;
    }

    public function toUnit(WeightUnit $destinationUnit): self
    {
        if ($this->unit() === $destinationUnit) {
            return $this;
        }

        $conversionRate = $this->unit->getConversionRate($destinationUnit);
        $newValue = $this->value * $conversionRate;

        return new self((float) $newValue, $destinationUnit);
    }

    private function validate(float $value): void
    {
        if ($value < 0) {
            throw new WeightException('Weight cannot be negative');
        }
    }
}
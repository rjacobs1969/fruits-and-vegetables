<?php

declare(strict_types=1);

namespace App\Shared\Domain;

use ValueError;

enum WeightUnit: string
{
    private const GRAMS_IN_KILOGRAM = 1000;
    private const DEFAULT_UNIT = self::GRAM;

    case GRAM = 'g';
    case KILOGRAM = 'kg';

    public static function fromString(?string $unit): self
    {
        try {
            if (null === $unit) {
                return self::DEFAULT_UNIT;
            }

            return self::from($unit);
        } catch (ValueError) {
            throw new WeightUnitException("Invalid weight unit: $unit");
        }
    }

    public function getConversionRate(WeightUnit $toUnit): float
    {
        return match ($this) {
            self::GRAM => match ($toUnit) {
                self::GRAM => 1.0,
                self::KILOGRAM => 1.0 / self::GRAMS_IN_KILOGRAM,
            },
            self::KILOGRAM => match ($toUnit) {
                self::GRAM => self::GRAMS_IN_KILOGRAM,
                self::KILOGRAM => 1.0,
            },
        };
    }
}
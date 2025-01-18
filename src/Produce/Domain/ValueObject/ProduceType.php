<?php

declare(strict_types=1);

namespace App\Produce\Domain\ValueObject;

use App\Produce\Domain\Exception\ProduceTypeException;
use ValueError;

enum ProduceType: string
{
    case FRUIT = 'fruit';
    case VEGETABLE = 'vegetable';

    public static function fromString(string $type): ?self
    {
        try {
            return self::from($type);
        } catch (ValueError) {
            throw new ProduceTypeException("Invalid produce type: $type");
        }
    }

    public function equals(ProduceType $otherType): bool
    {
        return $this === $otherType;
    }
}
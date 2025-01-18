<?php

declare(strict_types=1);

namespace App\Produce\Domain\Collection;

use App\Produce\Domain\ValueObject\ProduceType;

class VegetablesCollection extends ProduceCollection
{
    protected const ?ProduceType PRODUCE_TYPE_FILTER = ProduceType::VEGETABLE;
}
<?php

declare(strict_types=1);

namespace App\Produce\Application\Transformer;

use App\Produce\Domain\Collection\ProduceCollection;
use App\Produce\Domain\Entity\Produce;
use App\Shared\Domain\WeightUnit;

class ProduceTransformer
{
    public function transformCollection(ProduceCollection $collection, WeightUnit $displayWeightUnit): array
    {
        $result = [];
        foreach($collection->list() as $produce) {
            $result[] = $this->transformProduce($produce, $displayWeightUnit);
        }

        return $result;
    }

    public function transformProduce(Produce $produce, WeightUnit $displayWeightUnit): array
    {
        return [
            'id' => $produce->getId(),
            'name' => $produce->getName(),
            'type' => $produce->getType(),
            'quantity' => $produce->getWeight()->toUnit($displayWeightUnit),
            'unit' => $displayWeightUnit->value,
        ];
    }
}
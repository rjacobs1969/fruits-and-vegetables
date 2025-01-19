<?php

declare(strict_types=1);

namespace App\Produce\Application\Transformer;

use App\Produce\Domain\Collection\ProduceCollection;
use App\Produce\Domain\Entity\Produce;
use App\Shared\Domain\SearchRequest;
use App\Shared\Domain\WeightUnit;

class ProduceTransformer
{
    public function transform(ProduceCollection $collection, SearchRequest $searchRequest): array
    {
        $displayWeightUnit = $searchRequest->weightUnit();
        if ($searchRequest->id() !== null && $collection->count() === 1) {
            return $this->transformProduce($collection->find($searchRequest->id()), $displayWeightUnit);
        }

        return $this->transformCollection($collection, $displayWeightUnit);
    }

    public function transformCollection(ProduceCollection $collection, ?WeightUnit $displayWeightUnit = null): array
    {
        $result = [];
        foreach($collection->list() as $produce) {
            $result[] = $this->transformProduce($produce, $displayWeightUnit);
        }

        return $result;
    }

    public function transformProduce(Produce $produce, ?WeightUnit $displayWeightUnit = null): array
    {
        if ($displayWeightUnit === null){
            $displayWeightUnit = $produce->getWeight()->unit();
        }

        return [
            'id' => $produce->getId(),
            'name' => $produce->getName(),
            'type' => $produce->getType(),
            'quantity' => $produce->getWeight()->toUnit($displayWeightUnit)->value(),
            'unit' => $displayWeightUnit->value,
        ];
    }
}
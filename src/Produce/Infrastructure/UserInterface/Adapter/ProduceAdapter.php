<?php

declare(strict_types=1);

namespace App\Produce\Infrastructure\UserInterface\Adapter;

use App\Produce\Domain\Collection\ProduceCollection;
use App\Produce\Domain\Entity\Produce;

class ProduceAdapter
{
    public function adaptToDomain(array $data): Produce
    {
        return new Produce(
            isset($data['id']) ? (int) $data['id'] : null,
            (string) $data['name'] ?? '',
            (string) $data['type'] ?? '',
            (float)  $data['quantity'] ?? 0,
            (string) $data['unit'] ?? 'g'
        );
    }

    public function adaptFromArray(array $data): ProduceCollection
    {
        $collection = new ProduceCollection();
        foreach ($data as $item) {
            $collection->add($this->adaptToDomain($item));
        }

        return $collection;
    }
}

<?php

declare(strict_types=1);

namespace App\Produce\Infrastructure\UserInterface\Adapter;

use App\Produce\Domain\Entity\Produce;
use App\Produce\Domain\Collection\ProduceCollection;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Throwable;

class ProduceAdapter
{
    public function adaptToDomain(array $data): Produce
    {
        try {
            return new Produce(
                isset($data['id']) ? (int) $data['id'] : null,
                (string) $data['name'] ?? '',
                (string) $data['type'] ?? '',
                (float)  $data['quantity'] ?? 0,
                (string) $data['unit'] ?? 'g'
            );
        } catch (Throwable $e) {
            throw new BadRequestException($e->getMessage());
        }
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

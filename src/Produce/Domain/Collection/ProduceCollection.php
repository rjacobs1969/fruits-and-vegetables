<?php

declare(strict_types=1);

namespace App\Produce\Domain\Collection;

use App\Produce\Domain\Entity\Produce;
use App\Produce\Domain\Exception\ProduceException;

class ProduceCollection extends AbstractProduceCollection
{
    private array $items = [];

    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            if (!$item instanceOf Produce) {
                throw new ProduceException("Invalid Element");
            }

            $this->add($item);
        }
    }

    public function find(int $id): ?Produce
    {
        $arrayKey = $this->findArrayKeyById($id);
        if ($arrayKey !== null) {
            return $this->items[$arrayKey];
        }

        return null;
    }

    public function add(Produce $item): void
    {
        if (static::PRODUCE_TYPE_FILTER !== null && $item->getType() !== static::PRODUCE_TYPE_FILTER) {
            throw new ProduceException("Not the correct type");
        }

        $this->items[] = $item;
    }

    public function list(): array
    {
        return array_values($this->items);
    }

    public function remove(Produce $itemToRemove): void
    {
        foreach ($this->items as $key => $item) {
            if ($item === $itemToRemove) {
                unset($this->items[$key]);
            }
        }
    }

    public function removeById(int $id): void
    {
        $arrayKey = $this->findArrayKeyById($id);
        if ($arrayKey !== null) {
            unset($this->items[$arrayKey]);
        }
    }

    private function findArrayKeyById(int $id): ?int
    {
        foreach ($this->items as $key => $item) {
            if ($item->getId() === $id) {
                return $key;
            }
        }

        return null;
    }
}
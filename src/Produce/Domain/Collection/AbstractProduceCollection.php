<?php

declare(strict_types=1);

namespace App\Produce\Domain\Collection;

use App\Produce\Domain\Entity\Produce;
use App\Produce\Domain\ValueObject\ProduceType;
use ArrayIterator;
use Countable;
use IteratorAggregate;

abstract class AbstractProduceCollection implements IteratorAggregate, Countable
{
    protected const ?ProduceType PRODUCE_TYPE_FILTER = null;

    abstract public function add(Produce $item): void;
    abstract public function remove(Produce $item): void;
    abstract public function removeById(int $id): void;
    abstract public function find(int $id): ?Produce;
    abstract public function list(): array;

    public static function fromCollection(ProduceCollection $sourceCollection): self
    {
        return new static($sourceCollection->listByType(static::PRODUCE_TYPE_FILTER));
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->list());
    }

    public function count(): int
    {
        return count($this->list());
    }

    public function contains(Produce $item): bool
    {
        foreach ($this->list() as $produce) {
            if ($produce === $item) {
                return true;
            }
        }

        return false;
    }

    public function collectionType(): ProduceType
    {
        return static::PRODUCE_TYPE_FILTER;
    }

    protected function listByType(ProduceType $type): array
    {
        return array_values(array_filter($this->list(), function (Produce $item) use ($type) {
            return $item->getType()->equals($type);
        }));
    }
}
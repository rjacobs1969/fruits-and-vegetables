<?php

namespace App\Tests\Domain;

use App\Produce\Domain\Collection\FruitsCollection;
use App\Produce\Domain\Collection\ProduceCollection;
use App\Produce\Domain\Entity\Produce;
use App\Produce\Domain\Exception\ProduceException;
use PHPUnit\Framework\TestCase;

class FruitsCollectionTest extends TestCase
{
    private FruitsCollection $collection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->collection = new FruitsCollection();
    }

    public function testCanAddItems(): void
    {
        $apple = new Produce(0, 'Apple', 'fruit', 1500);
        $banana = new Produce(1, 'Banana', 'fruit', 1000);

        $this->collection->add($apple);
        $this->collection->add($banana);

        $this->assertCount(2, $this->collection);
        $this->assertSame($apple, $this->collection->find(0));
        $this->assertSame($banana, $this->collection->find(1));
    }

    public function testCannotAddItemOfWrongType(): void
    {
        $carrot = new Produce(2, 'Carrot', 'vegetable', 500);

        $this->expectException(ProduceException::class);
        $this->collection->add($carrot);
    }

    public function testCanCreateCollectionFromParent(): void
    {
        $carrot = new Produce(0, 'Carrot', 'vegetable', 500);
        $cauliflower = new Produce(1, 'cauliflower', 'vegetable', 2000);
        $apple = new Produce(0, 'Apple', 'fruit', 1500);

        $parentCollection = new ProduceCollection([
            $carrot,
            $apple,
            $cauliflower
        ]);

        $this->collection = FruitsCollection::fromCollection($parentCollection);

        $this->assertCount(1, $this->collection);
        $this->assertFalse($this->collection->contains($carrot));
        $this->assertTrue($this->collection->contains($apple));
        $this->assertFalse($this->collection->contains($cauliflower));
    }
}
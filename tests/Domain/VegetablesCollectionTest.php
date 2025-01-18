<?php

namespace App\Tests\Domain;

use App\Produce\Domain\Collection\ProduceCollection;
use App\Produce\Domain\Collection\VegetablesCollection;
use App\Produce\Domain\Entity\Produce;
use App\Produce\Domain\Exception\ProduceException;
use PHPUnit\Framework\TestCase;

class VegetablesCollectionTest extends TestCase
{
    private VegetablesCollection $collection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->collection = new VegetablesCollection();
    }

    public function testCanAddItems(): void
    {
        $carrot = new Produce(0, 'Carrot', 'vegetable', 500);
        $cauliflower = new Produce(1, 'cauliflower', 'vegetable', 2000);


        $this->collection->add($carrot);
        $this->collection->add($cauliflower);

        $this->assertCount(2, $this->collection);
        $this->assertSame($carrot, $this->collection->find(0));
        $this->assertSame($cauliflower, $this->collection->find(1));
    }

    public function testCannotAddItemOfWrongType(): void
    {
        $apple = new Produce(0, 'Apple', 'fruit', 1500);

        $this->expectException(ProduceException::class);
        $this->collection->add($apple);
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

        $this->collection = VegetablesCollection::fromCollection($parentCollection);

        $this->assertCount(2, $this->collection);
        $this->assertTrue($this->collection->contains($carrot));
        $this->assertFalse($this->collection->contains($apple));
        $this->assertTrue($this->collection->contains($cauliflower));
    }
}
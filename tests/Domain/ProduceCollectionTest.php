<?php

namespace App\Tests\Domain;

use App\Produce\Domain\Collection\ProduceCollection;
use App\Produce\Domain\Entity\Produce;
use App\Produce\Domain\Exception\ProduceException;
use PHPUnit\Framework\TestCase;
use stdClass;

class ProduceCollectionTest extends TestCase
{
    private ProduceCollection $collection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->collection = new ProduceCollection();
    }

    public function testAddItemToCollection(): void
    {
        $item = $this->createProduceItem(null, "Apple", "fruit");

        $this->collection->add($item);

        $this->assertCount(1, $this->collection);
        $this->assertTrue($this->collection->contains($item));
    }

    public function testNewCollectionWithArrayOfItems(): void
    {
        $items = [
            $this->createProduceItem(1, "Apple", "fruit"),
            $this->createProduceItem(2, "Carrot", "vegetable"),
            $this->createProduceItem(3, "Eggplant", "vegetable"),
        ];

        $collection = new ProduceCollection($items);

        $this->assertCount(3, $collection);
    }

    public function testExceptionWhenCreateCollectionWithItemsOfWrongType(): void
    {
        $this->expectException(ProduceException::class);
        new ProduceCollection([new stdClass()]);
    }

    public function testFindItemInCollection(): void
    {
        $item1 = $this->createProduceItem(1, "Apple", "fruit");
        $item2 = $this->createProduceItem(2, "Carrot", "vegetable");

        $this->collection->add($item1);
        $this->collection->add($item2);

        $foundItem = $this->collection->find(1);

        $this->assertNotNull($foundItem);
        $this->assertEquals($item1, $foundItem);
    }

    public function testReturnNullWhenItemNotFound(): void
    {
        $item1 = $this->createProduceItem(1, "Apple", "fruit");
        $item2 = $this->createProduceItem(2, "Carrot", "vegetable");

        $this->collection->add($item1);
        $this->collection->add($item2);

        $foundItem = $this->collection->find(3);

        $this->assertNull($foundItem);
    }

    public function testRemoveItemFromCollection(): void
    {
        $item1 = $this->createProduceItem(1, "Apple", "fruit");
        $item2 = $this->createProduceItem(2, "Carrot", "vegetable");

        $this->collection->add($item1);
        $this->collection->add($item2);

        $this->collection->remove($item1);

        $this->assertCount(1, $this->collection);
        $this->assertFalse($this->collection->contains($item1));
        $this->assertTrue($this->collection->contains($item2));
    }

    public function testRemoveItemFromCollectionById(): void
    {
        $item1 = $this->createProduceItem(1, "Apple", "fruit");
        $item2 = $this->createProduceItem(2, "Carrot", "vegetable");

        $this->collection->add($item1);
        $this->collection->add($item2);

        $this->collection->removeById(2);

        $this->assertCount(1, $this->collection);
        $this->assertTrue($this->collection->contains($item1));
        $this->assertFalse($this->collection->contains($item2));
    }

    public function testListItemsInCollection(): void
    {
        $item1 = $this->createProduceItem(1, "Apple", "fruit");
        $item2 = $this->createProduceItem(2, "Carrot", "vegetable");

        $this->collection->add($item1);
        $this->collection->add($item2);

        $items = $this->collection->list();

        $this->assertCount(2, $items);
        $this->assertContains($item1, $items);
        $this->assertContains($item2, $items);
    }

    private function createProduceItem(?int $id, string $name, string $type)
    {
        return new Produce(
            $id,
            $name,
            $type,
            55
        );
    }
}
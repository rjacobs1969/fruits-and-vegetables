<?php

namespace App\Tests\Domain;

use App\Produce\Domain\Entity\Produce;
use App\Produce\Domain\Exception\ProduceException;
use App\Produce\Domain\Exception\ProduceTypeException;
use App\Produce\Domain\ValueObject\ProduceType;
use App\Shared\Domain\Weight;
use App\Shared\Domain\WeightUnit;
use PHPUnit\Framework\TestCase;

class ProduceTest extends TestCase
{
    public function testCanCreate(): void
    {
        $produceItem = new Produce(1, "Apple", "fruit", 1000);

        $this->assertInstanceOf(Produce::class, $produceItem);
    }

    public function testCanCreateWithWeightUnit(): void
    {
        $produceItem = new Produce(1, "Apple", "fruit", 1, 'kg');

        $this->assertInstanceOf(Produce::class, $produceItem);
    }

    public function testCannotCreateWithUnknownType(): void
    {
        $this->expectException(ProduceTypeException::class);
        new Produce(1, "Apple", "unknown", 1000);
    }

    public function testCannotCreateWithNegativeId(): void
    {
        $this->expectException(ProduceException::class);
        new Produce(-1, "Apple", "fruit", 1000);
    }

    public function testCannotCreateWithEmptyName(): void
    {
        $this->expectException(ProduceException::class);
        new Produce(1, "", "fruit", 1000);
    }

    public function testCanSetAndGetId(): void
    {
        $produceItem = new Produce(1, "Apple", "fruit", 1000);
        $produceItem->setId(2);
        $this->assertEquals(2, $produceItem->getId());
    }

    public function testCanSetAndGetName(): void
    {
        $produceItem = new Produce(1, "Apple", "fruit", 1000);
        $produceItem->setName("Banana");
        $this->assertEquals("Banana", $produceItem->getName());
    }

    public function testCanSetAndGetType(): void
    {
        $produceItem = new Produce(1, "Apple", "fruit", 1000);
        $produceItem->setType(ProduceType::fromString("vegetable"));
        $this->assertEquals(ProduceType::fromString("vegetable"), $produceItem->getType());
    }

    public function testCanSetWeightInKilosAndGetWeightInGrams(): void
    {
        $produceItem = new Produce(1, "Apple", "fruit", 1000);
        $newWeight = new Weight(2, WeightUnit::from('kg'));
        $produceItem->setWeight($newWeight);

        $this->assertEquals($newWeight->toUnit(WeightUnit::GRAM), $produceItem->getWeight());
        $this->assertEquals(2000, $produceItem->getWeight()->value());
    }
}
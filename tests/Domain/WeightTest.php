<?php

namespace App\Tests\Domain;

use App\Shared\Domain\Weight;
use App\Shared\Domain\WeightException;
use App\Shared\Domain\WeightUnit;
use PHPUnit\Framework\TestCase;

class WeightTest extends TestCase
{
    public function testCanCreateInGrams()
    {
        $weight = new Weight(100, WeightUnit::GRAM);
        $this->assertInstanceOf(Weight::class, $weight);
        $this->assertEquals(100, $weight->value());
        $this->assertEquals(WeightUnit::GRAM, $weight->unit());
    }

    public function testCanCreateInKilos()
    {
        $weight = new Weight(10, WeightUnit::KILOGRAM);
        $this->assertInstanceOf(Weight::class, $weight);
        $this->assertEquals(10, $weight->value());
        $this->assertEquals(WeightUnit::KILOGRAM, $weight->unit());
    }

    public function testWeightCannotBeNegative()
    {
        $this->expectException(WeightException::class);
        new Weight(-100, WeightUnit::GRAM);
    }

    public function testConvertGramsToKilos()
    {
        $weight = new Weight(1000, WeightUnit::GRAM);
        $convertedWeight = $weight->toUnit(WeightUnit::KILOGRAM);

        $this->assertEquals(1, $convertedWeight->value());
        $this->assertEquals(WeightUnit::KILOGRAM, $convertedWeight->unit());
    }

    public function testConvertKilosToGrams()
    {
        $weight = new Weight(10, WeightUnit::KILOGRAM);
        $convertedWeight = $weight->toUnit(WeightUnit::GRAM);

        $this->assertEquals(10000, $convertedWeight->value());
        $this->assertEquals(WeightUnit::GRAM, $convertedWeight->unit());
    }

    public function testNoConversionWithEqualSourceAndDestinationUnits()
    {
        $weight = new Weight(500, WeightUnit::GRAM);
        $convertedWeight = $weight->toUnit(WeightUnit::GRAM);

        $this->assertSame($weight, $convertedWeight);
    }
}

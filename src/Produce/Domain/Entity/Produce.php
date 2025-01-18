<?php

declare(strict_types=1);

namespace App\Produce\Domain\Entity;

use App\Produce\Domain\Exception\ProduceException;
use App\Produce\Domain\ValueObject\ProduceType;
use App\Shared\Domain\Weight;
use App\Shared\Domain\WeightUnit;

class Produce
{
    private ?int $id;
    private string $name;
    private ProduceType $type;
    private Weight $weight;

    public function __construct(
        ?int $id,
        string $name,
        string $type,
        int $quantity,
        string $weightUnit = 'g'
    ) {
        $this->setId($id);
        $this->setName($name);
        $this->type = ProduceType::fromString($type);
        $originalWeight = new Weight($quantity, WeightUnit::from($weightUnit));
        $this->weight = $originalWeight->toUnit(WeightUnit::GRAM);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ProduceType
    {
        return $this->type;
    }

    public function getWeight(): Weight
    {
        return $this->weight;
    }

    public function setId(?int $id): void
    {
        if ($id !== null && $id < 0) {
            throw new ProduceException("Invalid id: " . $id);
        }
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        if (empty($name)) {
            throw new ProduceException("Name cannot be empty");
        }
        $this->name = $name;
    }

    public function setType(ProduceType $type): void
    {
        $this->type = $type;
    }

    public function setWeight(Weight $weight): void
    {
        $this->weight = $weight->toUnit(WeightUnit::GRAM);
    }
}
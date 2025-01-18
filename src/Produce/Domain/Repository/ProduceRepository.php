<?php

namespace App\Produce\Domain\Repository;

use App\Produce\Domain\Collection\ProduceCollection;
use App\Produce\Domain\Entity\Produce;
use App\Shared\Domain\Criteria;

interface ProduceRepository
{
    public function findAll(): ProduceCollection;
    public function findById(int $id): ?Produce;
    public function findByCriteria(Criteria $criteria): ProduceCollection;
    public function create(Produce $produce): void;
}
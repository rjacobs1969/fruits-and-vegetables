<?php

namespace App\Produce\Domain\Repository;

use App\Produce\Domain\Collection\ProduceCollection;
use App\Produce\Domain\Entity\Produce;
use App\Shared\Domain\Criteria;

interface ProduceRepository
{
    public function create(Produce $produce): void;
    public function retrieve(Criteria $criteria): ProduceCollection;
    public function update(Produce $produce): void;
    public function delete(int $id): void;
}
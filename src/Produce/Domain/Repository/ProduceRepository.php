<?php

namespace App\Produce\Domain\Repository;

use App\Shared\Domain\Criteria;

interface ProduceRepository
{
    public function findAll();

    public function findById(int $id);// : ?Produce;

    public function findByCriteria(Criteria $criteria);

    //public function save(Produce $produce): void;

}
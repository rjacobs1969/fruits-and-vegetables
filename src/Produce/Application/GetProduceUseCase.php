<?php

declare(strict_types=1);

namespace App\Produce\Application;

use App\Produce\Domain\Repository\ProduceRepository;
use App\Shared\Domain\SearchRequest;
use App\Shared\Domain\Criteria;
use App\Shared\Domain\WeightUnit;

final class GetProduceUseCase
{
    public function __construct(private ProduceRepository $produceRepository) {}

    public function execute(SearchRequest $request): array
    {
        if ($request->type() !== null || $request->name() !== null) {
            $produceCollection = $this->produceRepository->findByCriteria(Criteria::createFromSearchRequest($request));
        } elseif ($request->id() !== null) {
            $produceCollection = $this->produceRepository->findById($request->id());
        } else {
            $produceCollection = $this->produceRepository->findAll();
        }
        return [
            "he" => 'ok',
            "id" => $request->id(),
            "type" => $request->type(),
            "name" => $request->name(),
            "unit" => $request->weightUnit()
        ];

        return $this->outputTransformer($produceCollection, $request->weightUnit());
    }

    public function outputTransformer($produceCollection, WeightUnit $weightUnit): array
    {
        $result = [];
        foreach ($produceCollection as $produce) {


        }

        return $result;
    }
}
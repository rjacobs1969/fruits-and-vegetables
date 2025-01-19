<?php

declare(strict_types=1);

namespace App\Produce\Application;

use App\Produce\Application\Transformer\ProduceTransformer;
use App\Produce\Domain\Repository\ProduceRepository;
use App\Shared\Domain\SearchRequest;
use App\Shared\Domain\Criteria;

final class GetProduceUseCase
{
    public function __construct(private ProduceRepository $produceRepository, private ProduceTransformer $transformer) {}

    public function execute(SearchRequest $request): array
    {
        $criteria = Criteria::createFromSearchRequest($request);
        $produceCollection = $this->produceRepository->retrieve($criteria);

        return $this->transformer->transformCollection($produceCollection, $request->weightUnit());
    }
}
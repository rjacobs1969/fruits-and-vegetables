<?php

declare(strict_types=1);

namespace App\Produce\Application\UseCase;

use App\Produce\Application\Transformer\ProduceTransformer;
use App\Produce\Domain\Entity\Produce;
use App\Produce\Domain\Repository\ProduceRepository;

final class UpdateProduceUseCase
{
    public function __construct(private ProduceRepository $produceRepository, private ProduceTransformer $transformer) {}

    public function execute(Produce $produce): array
    {
        $this->produceRepository->update($produce);

        return $this->transformer->transformProduce($produce);
    }
}
<?php

declare(strict_types=1);

namespace App\Produce\Application\UseCase;

use App\Produce\Application\Transformer\ProduceTransformer;
use App\Produce\Domain\Repository\ProduceRepository;
use App\Shared\Domain\PersistException;

final class DeleteProduceUseCase
{
    public function __construct(private ProduceRepository $produceRepository, private ProduceTransformer $transformer) {}

    public function execute(int $id): void
    {
        if ($id < 1) {
            throw new PersistException("Invallid id ".$id);
        }

        $this->produceRepository->delete($id);
    }
}
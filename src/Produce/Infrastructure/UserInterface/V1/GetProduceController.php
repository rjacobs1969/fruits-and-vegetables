<?php

declare(strict_types=1);

namespace App\Produce\Infrastructure\UserInterface\V1;

use App\Produce\Application\GetProduceUseCase;
use App\Produce\Domain\ValueObject\ProduceType;
use App\Shared\Domain\DomainException;
use App\Shared\Domain\SearchRequest;
use App\Shared\Domain\WeightUnit;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Throwable;

use function PHPUnit\Framework\isEmpty;

#[Route('/api/v1/produce/{id<\d+>}', name: 'list_produce', methods: ['GET'])]
final class GetProduceController extends AbstractController
{
    public function __construct(private GetProduceUseCase $useCase) {}

    public function __invoke(Request $request, ?int $id = null): JsonResponse
    {
        try {
            $SearchRequest = $this->adaptHttpRequestToDomainRequest($request, $id);
            $result = $this->useCase->execute($SearchRequest);

            return new JsonResponse($result);
        } catch (BadRequestException | DomainException $e) {
            return new JsonResponse('Bad request: '.$e->getMessage(),Response::HTTP_BAD_REQUEST);
        } catch (Throwable $e) {
            return new JsonResponse('Internal server error '.$e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function adaptHttpRequestToDomainRequest(Request $request, ?int $id): SearchRequest
    {
        $type = $request->query->get('type');
        if ($type !== null && ProduceType::tryFrom($type) === null) {
			throw new BadRequestException("Optional type filter parameter given but not valid: " . $type);
		}

        $name = $request->query->get('name');
        if ($name !== null && !is_string($name)) {
			throw new BadRequestException("Optional name filter parameter given but not valid: " . $name);
		}

        $weightUnit = $request->query->get('unit');

        return new SearchRequest(
            $id,
            $name,
            ProduceType::fromString($type),
            WeightUnit::fromString($weightUnit)
        );
    }
}


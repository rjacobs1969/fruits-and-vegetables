<?php

declare(strict_types=1);

namespace App\Produce\Infrastructure\UserInterface\V1;

use App\Produce\Application\UseCase\CreateProduceUseCase;
use App\Produce\Domain\Entity\Produce;
use DomainException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Throwable;

#[Route('/api/v1/produce/', name: 'list_produce', methods: ['POST'])]
final class GetProduceController extends AbstractController
{
    public function __construct(private CreateProduceUseCase $useCase) {}

    public function __invoke(Request $request)
    {
        try {
            $postContent = $this->getValidatedPostContent($request);
            $newProduce = $this->adaptToDomain($postContent);
            $result = $this->useCase->execute($newProduce);

            return new JsonResponse($result, Response::HTTP_CREATED);

        } catch (BadRequestException | DomainException $e) {
            return new JsonResponse('Bad request: '.$e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (Throwable $e) {
            return new JsonResponse('Internal server error '.$e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function getValidatedPostContent(Request $request): array
    {
        if (!$request->getContent()) {
            throw new BadRequestException("Empty request");
        }

        $content = json_decode($request->getContent(), true);

        if (!is_array($content)) {
            throw new BadRequestException("Invalid request content");
        }

        return $content;
    }

    private function adaptToDomain(array $data): Produce
    {
        return new Produce(
            isset($data['id']) ? (int)$data['id'] : null,
            (string) $data['name'] ?? '',
            (string) $data['type'] ?? '',
            (int) $data['quantity'] ?? 0,
            (string) $data['unit'] ?? 'g'
        );
    }

}

<?php

declare(strict_types=1);

namespace App\Produce\Infrastructure\UserInterface\V1;

use App\Produce\Application\UseCase\CreateProduceUseCase;

use App\Produce\Infrastructure\UserInterface\Adapter\ProduceAdapter;
use App\Shared\Domain\DomainException;
use App\Shared\Domain\PersistException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Throwable;

#[Route('/api/v1/produce/', name: 'create_produce', methods: ['POST'])]
final class CreateProduceController extends AbstractController
{
    public function __construct(private CreateProduceUseCase $useCase, private ProduceAdapter $adapter) {}

    public function __invoke(Request $request)
    {
        try {
            $postContent = $this->getValidatedPostContent($request);
            $newProduce = $this->adapter->adaptToDomain($postContent);
            $result = $this->useCase->execute($newProduce);

            return new JsonResponse($result, Response::HTTP_CREATED);
        } catch (PersistException $e) {
            return new JsonResponse('Cannot create: '.$e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
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
}

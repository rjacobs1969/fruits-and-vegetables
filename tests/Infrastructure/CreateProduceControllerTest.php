<?php

namespace App\Test\Infrastructure;

use App\Produce\Infrastructure\Persistence\Database\Repository\ProduceDbalRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class CreateProduceControllerTest extends WebTestCase
{
    private const int ID_FOR_TEST = 3;

    private KernelBrowser $client;
    private $testRepository = null;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();
        $this->doCleanup();
    }

    protected function tearDown(): void
    {
        $this->doCleanup();
    }

    public function testCanCreateProduce(): void
    {
        $input = [
            'id' => 3,
            'name' => 'Green beans',
            'type' => 'vegetable',
            'quantity' => 500,
            'unit' => 'g'
        ];

        $this->doPostRequest(json_encode($input));
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertSame($input, $response);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testCanCreateProduceWithoutId(): void
    {
        $this->doPostRequest(json_encode(
            [
                'name' => 'Black beans',
                'type' => 'vegetable',
                'quantity' => 5,
                'unit' => 'kg'
            ]
        ));
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $createdId = $response['id'] ?? -1; // if key do not exist use invalid value
        $this->doCleanup($createdId);

        $this->assertGreaterThanOrEqual(0, $createdId);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testCannotCreateDuplicateIds(): void
    {
        $produce = json_encode([
            'id' => 1,
            'name' => 'Apple',
            'type' => 'fruit',
            'quantity' => 1250,
            'unit' => 'g'
        ]);

        $this->doPostRequest($produce);
        $this->doPostRequest($produce); // Posting the same id twice, second should fail

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testFailIfEmptyPostData(): void
    {
        $this->doPostRequest('');
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testFailIfMissingName(): void
    {
        $this->doPostRequest(json_encode(
            [
                'id' => self::ID_FOR_TEST,
                'name' => '', // name must not be empty
                'type' => 'vegetable',
                'quantity' => 5,
                'unit' => 'kg'
            ]
        ));
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testFailIfInvalidType(): void
    {
        $this->doPostRequest(json_encode(
            [
                'id' => self::ID_FOR_TEST,
                'name' => 'Steak',
                'type' => 'meat', // type incorrect
                'quantity' => 100,
                'unit' => 'g'
            ]
        ));
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testFailIfNegativeQuantity(): void
    {
        $this->doPostRequest(json_encode(
            [
                'id' => self::ID_FOR_TEST,
                'name' => 'Apple',
                'type' => 'fruit',
                'quantity' => -100,
                'unit' => 'kg'
            ]
        ));
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testFailIfInvalidUnit(): void
    {
        $this->doPostRequest(json_encode(
            [
                'id' => self::ID_FOR_TEST,
                'name' => 'Apple',
                'type' => 'fruit',
                'quantity' => 100,
                'unit' => 'oz' // incorrect
            ]
        ));
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    private function doPostRequest(string $content): void
    {
        $this->client->request(
            'POST',
            "/api/v1/produce/",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $content
        );
    }

    private function doCleanup(int $id = self::ID_FOR_TEST): void
    {
        if ($this->testRepository === null) {
            $this->testRepository = self::getContainer()->get(ProduceDbalRepository::class);
        }

        if ($id >= 0) {
            $this->testRepository->delete($id);
        }
    }
}
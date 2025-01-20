<?php

namespace App\Test\Infrastructure;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class GetProduceControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();
    }

    public function testGetAll(): void
    {
        $this->client->request('GET', "/api/v1/produce");

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
		$this->assertResponseHasHeader('Content-Type', 'application/json');
		$this->assertJson($this->client->getResponse()->getContent());
    }

    public function testGetById(): void
    {
        $this->client->request('GET', "/api/v1/produce/1");

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertJson($this->client->getResponse()->getContent());
    }

    public function testGetByType(): void
    {
        $this->client->request('GET', "/api/v1/produce?type=fruit");

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertJson($this->client->getResponse()->getContent());
    }

    public function testGetFailsWhenUsingIncorrectType(): void
    {
        $this->client->request('GET', "/api/v1/produce?type=meat");

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testGetByName(): void
    {
        $this->client->request('GET', "/api/v1/produce?name=apple");

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertJson($this->client->getResponse()->getContent());
    }

    public function testGetFailsWhenProvidingEmptyName(): void
    {
        $this->client->request('GET', "/api/v1/produce?name");

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testGetInGrams(): void
    {
        $this->client->request('GET', "/api/v1/produce/1?unit=g");

        $content = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(1250, $content['quantity'] ?? 'notset');
        $this->assertEquals('g', $content['unit'] ?? 'notset');
    }

    public function testGetInKilograms(): void
    {
        $this->client->request('GET', "/api/v1/produce/1?unit=kg");

        $content = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals(1.25, $content['quantity'] ?? 'notset');
        $this->assertEquals('kg', $content['unit'] ?? 'notset');
    }

    public function testGetFailsWhenUndefinedUnit(): void
    {
        $this->client->request('GET', "/api/v1/produce/1?unit=oz");

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}
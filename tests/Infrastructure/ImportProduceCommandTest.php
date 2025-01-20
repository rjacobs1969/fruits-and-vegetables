<?php

namespace App\Test\Infrastructure;

use App\Produce\Infrastructure\Command\ImportProduceCommand;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class ImportProduceCommandTest extends WebTestCase
{
    private CONST TEST_DATA_FILE = __DIR__.'/../TestData/TestData.json';

    private $command = null;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->command = self::getContainer()->get(ImportProduceCommand::class);
    }

    public function testImportOk(): void
    {
        $tester = new CommandTester($this->command);
        $tester->execute(['fileName' => self::TEST_DATA_FILE]);

        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());
    }
}
<?php

namespace App\Test\Infrastructure;

use App\Produce\Infrastructure\Command\ImportProduceCommand;
use App\Produce\Infrastructure\Persistence\Database\Repository\ProduceDbalRepository;
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
        $this->cleanUp();
    }

    public function testImportOk(): void
    {
        $tester = new CommandTester($this->command);
        $tester->execute(['fileName' => self::TEST_DATA_FILE]);

        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());
    }

    public function testFailImportTheSameDataTwice(): void
    {
        $tester = new CommandTester($this->command);
        $tester->execute(['fileName' => self::TEST_DATA_FILE]);
        $tester->execute(['fileName' => self::TEST_DATA_FILE]); // execute twice

        $this->assertEquals(Command::FAILURE, $tester->getStatusCode());
    }

    private function cleanup(): void
    {
        $testDatabaseRepository = self::getContainer()->get(ProduceDbalRepository::class);

        for ($id = 0; $id < 4; $id++) {
            $testDatabaseRepository->delete($id);
        }
    }
}
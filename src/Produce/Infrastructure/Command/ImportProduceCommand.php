<?php

declare(strict_types=1);

namespace App\Produce\Infrastructure\Command;

use App\Produce\Application\UseCase\UpdateProduceUseCase;
use App\Produce\Domain\Collection\FruitsCollection;
use App\Produce\Domain\Collection\ProduceCollection;
use App\Produce\Domain\Collection\VegetablesCollection;
use App\Produce\Infrastructure\UserInterface\Adapter\ProduceAdapter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(name: 'import')]
class ImportProduceCommand extends Command
{
    private const FILE = 'fileName';

    public function __construct(private UpdateProduceUseCase $peristUseCase, private ProduceAdapter $adapter)
    {
        parent::__construct();
    }

    protected function configure(): void
	{
		$this->addArgument(self::FILE, InputArgument::REQUIRED, 'JSON File to import into the database');
	}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $file = $input->getArgument(self::FILE);
            $content = file_get_contents($file);
            $data = json_decode($content, true);

            if (empty($data)) {
                $output->writeln("Error: Could not decode JSON / No data");
                return Command::FAILURE;
            }

            $produceCollection = $this->adapter->adaptFromArray($data);
            $fruitsCollection = FruitsCollection::fromCollection($produceCollection);
            $vegetablesCollection = VegetablesCollection::fromCollection($produceCollection);

            $this->persistAll($fruitsCollection);
            $output->writeln(sprintf("%d %ss successfully imported", $fruitsCollection->count(), $fruitsCollection->collectionType()));

            $this->persistAll($vegetablesCollection);
            $output->writeln(sprintf("%d %ss successfully imported", $vegetablesCollection->count(), $vegetablesCollection->collectionType()));

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $output->writeln("Import failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function persistAll(ProduceCollection $collection): void
    {
        foreach ($collection->list() as $item) {
            $this->peristUseCase->execute($item);
        }
    }
}
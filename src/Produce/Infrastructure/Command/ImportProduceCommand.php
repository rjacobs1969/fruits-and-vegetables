<?php

declare(strict_types=1);

namespace App\Produce\Infrastructure\Command;

use App\Produce\Application\UseCase\CreateProduceUseCase;
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

    public function __construct(private CreateProduceUseCase $peristUseCase, private ProduceAdapter $adapter)
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

            $this->tryPersistAll($fruitsCollection, $output);
            $this->tryPersistAll($vegetablesCollection, $output);

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $output->writeln("Import failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function tryPersistAll(ProduceCollection $collection, OutputInterface $output): void
    {
        $numberOfPersistSucces = 0;
        foreach ($collection->list() as $item) {
            try {
                $this->peristUseCase->execute($item);
                $numberOfPersistSucces++;
            } catch (Throwable $e) {
                $output->writeln(
                    sprintf(
                        "Failed storing %d %s: %s \n",
                        $item->getId() ?? '',
                        $item->getName(),
                        $e->getMessage()
                    ),
                    $output::VERBOSITY_VERBOSE
                );
                // Don't stop on failure of storing this item, continue with the rest
            }
        }
        $output->writeln(
            sprintf(
                "%d out of %d %ss successfully imported",
                $numberOfPersistSucces,
                $collection->count(),
                $collection->collectionType()->value
            )
        );

        if ($numberOfPersistSucces < $collection->count() && !$output->isVerbose()) {
            $output->writeln(
                sprintf(
                    "%d %ss failed to import, use verbose flag -v to see details",
                    $collection->count()-$numberOfPersistSucces,
                    $collection->collectionType()->value
                )
            );
        }
    }
}
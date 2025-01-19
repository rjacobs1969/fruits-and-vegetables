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

use function PHPUnit\Framework\isNull;

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

            $allFruitsImported = $this->tryPersistAll($fruitsCollection, $output);
            $allVegiesImported = $this->tryPersistAll($vegetablesCollection, $output);

            if ($allFruitsImported === false || $allVegiesImported === false) {
                $output->writeln(
                        sprintf(
                            "Import finished with failures %s",
                            (!$output->isVerbose()) ? ', use verbose flag -v to see details' : '.'
                        )
                    );
                return Command::FAILURE;
            }

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $output->write("Import failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function tryPersistAll(ProduceCollection $collection, OutputInterface $output): bool
    {
        $numberOfPersistSucces = 0;
        foreach ($collection->list() as $item) {
            try {
                $this->peristUseCase->execute($item);
                $numberOfPersistSucces++;
            } catch (Throwable $e) {
                $output->write(
                    sprintf(
                        "Failed storing %s: %s",
                        $item->getName(),
                        $e->getMessage()
                    ),
                    true,
                    $output::VERBOSITY_VERBOSE
                );
                throw $e;
                // Don't stop on failure of storing this item, continue with the rest
            }
        }
        $output->writeln(
            sprintf(
                "%d out of %d %ss successfully imported",
                $numberOfPersistSucces,
                $collection->count(),
                $collection->collectionType()
            )
        );

        $isSuccess = ($numberOfPersistSucces === $collection->count());

        return $isSuccess;
    }
}
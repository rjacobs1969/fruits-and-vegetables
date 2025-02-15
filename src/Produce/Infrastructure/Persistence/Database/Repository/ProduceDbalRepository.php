<?php

declare(strict_types=1);

namespace App\Produce\Infrastructure\Persistence\Database\Repository;

use App\Produce\Domain\Collection\ProduceCollection;
use App\Produce\Domain\Entity\Produce;
use App\Produce\Domain\Repository\ProduceRepository;
use App\Produce\Infrastructure\Persistence\Database\Adapter\ProduceAdapter;
use App\Shared\Domain\Criteria;
use App\Shared\Domain\PersistException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Doctrine\DBAL\Exception\DatabaseObjectExistsException;
use Exception;

class ProduceDbalRepository implements ProduceRepository
{
    private const DATABASE_TABLE = 'produce';

    public function __construct(private Connection $connection, private ProduceAdapter $adapter) {}

    public function create(Produce $produce): void
    {
        try {
            $data = $this->adapter->convertToDatabaseValues($produce);
            $this->connection->insert(self::DATABASE_TABLE, $data);
            $produce->setId((int) $this->connection->lastInsertId());
        } catch (ConstraintViolationException | DatabaseObjectExistsException $e) {
            throw new PersistException('Item already exists, id: '.$produce->getId());
        } catch (Exception $e) {
            throw new PersistException('Item not saved: '.$e->getMessage());
        }
    }

    public function retrieve(Criteria $criteria): ProduceCollection
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->select(
                ProduceAdapter::DB_ID_FIELD,
                ProduceAdapter::DB_NAME_FIELD,
                ProduceAdapter::DB_TYPE_FIELD,
                ProduceAdapter::DB_WEIGHT_FIELD
            )->from(self::DATABASE_TABLE);

        foreach ($criteria->filters as $filterName => $value) {
            $field = $this->adapter->getFieldFromName($filterName);
            $queryBuilder->andWhere("$field = :$field");
            $queryBuilder->setParameter($field, $value, $this->adapter->getParameterType($field));
        }
        $raw = $queryBuilder->executeQuery()->fetchAllAssociative();

        return $this->adapter->toCollection($raw);
    }

    public function update(Produce $produce): void
    {
        try {
            $this->create($produce);

            return;
        } catch (PersistException $e) {
            // Continue using update
        }

        try {
            $this->connection->update(
                self::DATABASE_TABLE,
                $this->adapter->convertToDatabaseValues($produce),
                [ProduceAdapter::DB_ID_FIELD => $produce->getId()]
            );
        } catch (Exception $e) {
            throw new PersistException('Item not updated: '.$e->getMessage());
        }
    }

    public function delete(int $id): void
    {
        $this->connection->delete(
            self::DATABASE_TABLE,
            [ProduceAdapter::DB_ID_FIELD => $id]
        );
    }
}
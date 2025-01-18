<?php

declare(strict_types=1);

namespace App\Produce\Infrastructure\Persistance\Database\Repository;

use App\Produce\Domain\Collection\ProduceCollection;
use App\Produce\Domain\Entity\Produce;
use App\Produce\Domain\Repository\ProduceRepository;
use App\Produce\Infrastructure\Persistance\Database\Adapter\ProduceAdapter;
use App\Shared\Domain\Criteria;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;

class ProduceDbalRepository implements ProduceRepository
{
    private const DATABASE_TABLE = 'produce';

    public function __construct(private Connection $connection, private ProduceAdapter $adapter) {}

    public function findAll(): ProduceCollection
    {
        $raw = $this->baseQuery()
                    ->executeQuery()
                    ->fetchAllAssociative();

        return $this->adapter->toCollection($raw);
    }

    public function findById(int $id): ?Produce
    {
        $raw = $this->baseQuery()
                ->where("id = :id")
                ->setParameter('id', $id, ParameterType::INTEGER)
                ->executeQuery()
                ->fetchAssociative();

        return $this->adapter->convertFromDatabaseValues($raw);
    }

    public function findByCriteria(Criteria $criteria): ProduceCollection
    {
        $query = $this->baseQuery();
        foreach ($criteria->filters as $filterName => $value) {
            $field = $this->adapter->getFieldFromName($filterName);
            $query->andWhere("$field = :$field")
                  ->setParameter($field, $value, $this->adapter->getParameterType($field));
        }
        $raw = $query->executeQuery()
                     ->fetchAllAssociative();

        return $this->adapter->toCollection($raw);
    }

    public function update(Produce $produce): void
    {
        $this->connection->update(
            self::DATABASE_TABLE,
            $this->adapter->convertToDatabaseValues($produce),
            [ProduceAdapter::DB_ID_FIELD => $produce->getId()]
        );
    }

    public function create(Produce $produce): void
    {
        $data = $this->adapter->convertToDatabaseValues($produce);
        $this->connection->insert(self::DATABASE_TABLE, $data);
        $produce->setId($this->connection->lastInsertId());
    }

    private function baseQuery(): QueryBuilder
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select(
                ProduceAdapter::DB_ID_FIELD,
                ProduceAdapter::DB_NAME_FIELD,
                ProduceAdapter::DB_TYPE_FIELD,
                ProduceAdapter::DB_TYPE_FIELD
            )
            ->from(self::DATABASE_TABLE);

        return $queryBuilder;
    }
}
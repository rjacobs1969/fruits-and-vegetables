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
    private const DATABASE_TABLE = 'produce.produce';

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
        $query = $this->baseQuery();
        foreach ($criteria->filters as $filterName => $value) {
            $field = $this->adapter->getFieldFromName($filterName);
            $query->andWhere("$field = :$field")
                ->setParameter($field, $value, $this->adapter->getParameterType($field));
        }
        $raw = $query->executeQuery()->fetchAllAssociative();

        return $this->adapter->toCollection($raw);
    }

    public function update(Produce $produce): void
    {
        try {
            $this->connection->update(
                self::DATABASE_TABLE,
                $this->adapter->convertToDatabaseValues($produce),
                [ProduceAdapter::DB_ID_FIELD => $produce->getId()]
            );
        } catch (Exception) {
            throw new PersistException('Item not updated');
        }
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
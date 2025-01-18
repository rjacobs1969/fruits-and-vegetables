<?php

declare(strict_types=1);

namespace App\Produce\Infrastructure\Persistance\Database\Adapter;

use App\Produce\Domain\Collection\ProduceCollection;
use App\Produce\Domain\Entity\Produce;
use Doctrine\DBAL\ParameterType;

final class ProduceAdapter
{
    public const DB_ID_FIELD = 'id';
    public const DB_NAME_FIELD = 'name';
    public const DB_TYPE_FIELD = 'type';
    public const DB_WEIGHT_FIELD = 'weight_in_grams';

    private const CRITERIA_FILTER_BY_ID = 'id';
    private const CRITERIA_FILTER_BY_NAME = 'name';
    private const CRITERIA_FILTER_BY_TYPE = 'type';

    private const DB_FIELD_TYPES = [
        self::DB_ID_FIELD     => ParameterType::INTEGER,
        self::DB_NAME_FIELD   => ParameterType::STRING,
        self::DB_TYPE_FIELD   => ParameterType::STRING,
        self::DB_WEIGHT_FIELD => ParameterType::INTEGER,
    ];

    private const FILTER_TO_FIELDS = [
        self::CRITERIA_FILTER_BY_ID   => self::DB_ID_FIELD,
        self::CRITERIA_FILTER_BY_NAME => self::DB_NAME_FIELD,
        self::CRITERIA_FILTER_BY_TYPE => self::DB_TYPE_FIELD,
    ];

    public function convertToDatabaseValues(Produce $produce): array
    {
        $data = [
            self::DB_NAME_FIELD => $produce->getName(),
            self::DB_TYPE_FIELD => $produce->getType()->value,
            self::DB_WEIGHT_FIELD => $produce->getWeight()->value(),
        ];

        if ($produce->getId() !== null) {
            $data[self::DB_ID_FIELD] = $produce->getId();
        }

        return $data;
    }

    public function convertFromDatabaseValues(array $raw): ?Produce
    {
        return new Produce(
            (int)    $raw[self::DB_ID_FIELD],
            (string) $raw[self::DB_NAME_FIELD],
            (string) $raw[self::DB_TYPE_FIELD],
            (int)    $raw[self::DB_WEIGHT_FIELD]
        );
    }

    public function toCollection(array $raws): ProduceCollection
    {
        $collection = new ProduceCollection();
        foreach ($raws as $raw) {
            $collection->add($this->convertFromDatabaseValues($raw));
        }

        return $collection;
    }

    public function getFieldFromName(string $name): string
    {
        return self::FILTER_TO_FIELDS[$name];
    }

    public function getParameterType(string $field): ParameterType
    {
        return self::DB_FIELD_TYPES[$field];
    }
}
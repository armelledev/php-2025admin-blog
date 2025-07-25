<?php

declare (strict_types=1);
namespace RectorPrefix202507;

use Rector\Config\RectorConfig;
use Rector\Doctrine\Bundle230\Rector\Class_\AddAnnotationToRepositoryRector;
use Rector\Doctrine\CodeQuality\Rector\Class_\MoveCurrentDateTimeDefaultInEntityToConstructorRector;
use Rector\Doctrine\CodeQuality\Rector\Class_\RemoveEmptyTableAttributeRector;
use Rector\Doctrine\CodeQuality\Rector\Property\CorrectDefaultTypesOnEntityPropertyRector;
use Rector\Doctrine\CodeQuality\Rector\Property\TypedPropertyFromColumnTypeRector;
use Rector\Doctrine\CodeQuality\Rector\Property\TypedPropertyFromToOneRelationTypeRector;
use Rector\Transform\Rector\Attribute\AttributeKeyToClassConstFetchRector;
use Rector\Transform\ValueObject\AttributeKeyToClassConstFetch;
return static function (RectorConfig $rectorConfig) : void {
    $rectorConfig->rules([
        MoveCurrentDateTimeDefaultInEntityToConstructorRector::class,
        CorrectDefaultTypesOnEntityPropertyRector::class,
        RemoveEmptyTableAttributeRector::class,
        // typed properties in entities from annotations/attributes
        TypedPropertyFromColumnTypeRector::class,
        TypedPropertyFromToOneRelationTypeRector::class,
        // annotations generics
        AddAnnotationToRepositoryRector::class,
    ]);
    $rectorConfig->ruleWithConfiguration(AttributeKeyToClassConstFetchRector::class, [new AttributeKeyToClassConstFetch('Doctrine\\ORM\\Mapping\\Column', 'type', 'Doctrine\\DBAL\\Types\\Types', ['array' => 'ARRAY', 'ascii_string' => 'ASCII_STRING', 'bigint' => 'BIGINT', 'binary' => 'BINARY', 'blob' => 'BLOB', 'boolean' => 'BOOLEAN', 'date' => 'DATE_MUTABLE', 'date_immutable' => 'DATE_IMMUTABLE', 'dateinterval' => 'DATEINTERVAL', 'datetime' => 'DATETIME_MUTABLE', 'datetime_immutable' => 'DATETIME_IMMUTABLE', 'datetimetz' => 'DATETIMETZ_MUTABLE', 'datetimetz_immutable' => 'DATETIMETZ_IMMUTABLE', 'decimal' => 'DECIMAL', 'float' => 'FLOAT', 'guid' => 'GUID', 'integer' => 'INTEGER', 'json' => 'JSON', 'object' => 'OBJECT', 'simple_array' => 'SIMPLE_ARRAY', 'smallint' => 'SMALLINT', 'string' => 'STRING', 'text' => 'TEXT', 'time' => 'TIME_MUTABLE', 'time_immutable' => 'TIME_IMMUTABLE'])]);
};

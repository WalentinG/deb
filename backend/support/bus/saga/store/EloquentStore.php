<?php

declare(strict_types=1);

namespace support\bus\saga\store;

use support\bus\saga\exception\SagaNotFound;
use support\bus\saga\Saga;
use support\bus\saga\Sagas;
use support\Db;

final class EloquentStore implements Sagas
{
    public function get(string $sagaClass, string $tableName, array $criteria): Saga
    {
        $saga = Db::table($tableName)->where($criteria)->first();
        if (null === $saga) {
            throw new SagaNotFound($sagaClass);
        }

        return unmarshal($sagaClass, snakeToCamel((array)$saga));
    }

    public function id(array $id, string $sagaClass, string $tableName, array $criteria): array
    {
        $saga = Db::table($tableName)->select($id)->where($criteria)->first();
        if (null === $saga) {
            throw new SagaNotFound($sagaClass);
        }
        /* @phpstan-ignore-next-line */
        return (array)$saga;
    }

    public function create(Saga $saga, string $tableName): void
    {
        Db::table($tableName)->insert(camelToSnake(marshal($saga)));
    }

    public function update(Saga $saga, string $tableName, array $criteria): void
    {
        Db::table($tableName)->where($criteria)->update(camelToSnake(marshal($saga)));
    }
}

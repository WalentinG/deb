<?php

declare(strict_types=1);

namespace support\bus\saga;

interface Sagas
{
    /**
     * @param non-empty-array<non-empty-string, float|int|bool|string> $criteria
     * @param class-string<Saga>                                       $sagaClass
     * @param non-empty-string                                         $tableName
     */
    public function get(string $sagaClass, string $tableName, array $criteria): Saga;

    /**
     * @param non-empty-array<non-empty-string, float|int|bool|string> $criteria
     * @param non-empty-array<string>                                  $id
     * @param non-empty-string                                         $tableName
     * @param class-string<Saga>                                       $sagaClass
     *
     * @return non-empty-array<non-empty-string, float|int|string|bool>
     */
    public function id(array $id, string $sagaClass, string $tableName, array $criteria): array;

    /**
     * @param non-empty-string $tableName
     */
    public function create(Saga $saga, string $tableName): void;

    /**
     * @param non-empty-string                               $tableName
     * @param non-empty-array<string, float|int|string|bool> $criteria
     */
    public function update(Saga $saga, string $tableName, array $criteria): void;
}

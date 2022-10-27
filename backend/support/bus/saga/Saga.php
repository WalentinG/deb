<?php

declare(strict_types=1);

namespace support\bus\saga;

interface Saga
{
    /** @return array<non-empty-string, float|int|string|bool> */
    public static function criteriaToFindSaga(object $message): array;
}

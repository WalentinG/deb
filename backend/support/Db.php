<?php

declare(strict_types=1);

/*
 * This file is part of the isun/api
 *
 * @see https://gitea.huayaygf.io/isun/api
 */

namespace support;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Query\Expression;

/**
 * Class Db.
 *
 * @method static void commit()
 * @method static array select(string $query, array $bindings = [], bool $useReadPdo = true)
 * @method static mixed transaction(\Closure $callback, int $attempts = 1)
 * @method static Expression raw($value)
 */
class Db extends Manager
{
    public static function uid(string $table, string $primary = 'id'): int
    {
        while (true) {
            $id = random_int(1000000000, 4000000000);
            if (!self::table($table)->where($primary, $id)->exists()) {
                return $id;
            }
        }
    }
}

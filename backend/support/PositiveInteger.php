<?php

declare(strict_types=1);

namespace support;

use Respect\Validation\Validator as v;
use function toInt;

abstract class PositiveInteger
{
    final public function __construct(public int $value)
    {
        v::positive()->setName(static::class)->check($value);
    }

    final public static function from(mixed $value): static
    {
        v::intVal()->setName(static::class)->check($value);

        return new static(toInt($value));
    }

    final public static function tryFrom(mixed $value): ?static
    {
        if (null === $value) {
            return null;
        }

        return static::from($value);
    }

    final public function eq(self $userId): bool
    {
        return $this->value === $userId->value;
    }

    final public function neq(self $userId): bool
    {
        return $this->value !== $userId->value;
    }
}

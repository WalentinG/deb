<?php

declare(strict_types=1);

namespace support;

use Respect\Validation\Validator as v;
use function toInt;

final class Count
{
    public function __construct(public int $value)
    {
    }

    public static function positive(mixed $value): self
    {
        v::positive()->min(1)->setName('count')->check($value);

        return self::from($value);
    }

    public static function from(mixed $value): self
    {
        v::intVal()->setName('count')->check($value);

        return new self(toInt($value));
    }

    public function minus(self|int $count): self
    {
        if ($count instanceof self) {
            $count = $count->value;
        }

        return self::from($this->value - $count);
    }

    public function empty(): bool
    {
        return 0 === $this->value;
    }

    public function isPositive(): bool
    {
        return $this->value > 0;
    }

    public function isNegative(): bool
    {
        return $this->value < 0;
    }
}

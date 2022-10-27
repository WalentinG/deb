<?php

declare(strict_types=1);

namespace support;

use Respect\Validation\Validator as v;

abstract class Amount
{
    final public function __construct(public float $value)
    {
    }

    /** @return static */
    final public static function from(mixed $value): static
    {
        v::numericVal()->setName('amount')->check($value);

        return new static(round(toFloat($value), 2));
    }

    final public static function positive(mixed $value): static
    {
        v::positive()->setName('amount')->check($value);

        return self::from($value);
    }

    final public function isEmpty(): bool
    {
        return empty($this->value);
    }

    final public function multiply(float $fee): static
    {
        return new static($this->value * $fee);
    }

    final public function plus(self $amount): static
    {
        return new static($this->value + $amount->value);
    }

    final public function minus(self $amount): static
    {
        return new static($this->value - $amount->value);
    }

    final public function gt(self|float|int $amount): bool
    {
        if ($amount instanceof self) {
            $amount = $amount->value;
        }

        return $this->value > $amount;
    }

    final public function gte(self|float|int $amount): bool
    {
        if ($amount instanceof self) {
            $amount = $amount->value;
        }

        return $this->value >= $amount;
    }

    final public function neq(self|float|int $amount): bool
    {
        if ($amount instanceof self) {
            $amount = $amount->value;
        }

        return $this->value !== toFloat($amount);
    }

    final public function eq(self|float|int $amount): bool
    {
        if ($amount instanceof self) {
            $amount = $amount->value;
        }

        return $this->value === toFloat($amount);
    }

    final public function lte(self|float|int $amount): bool
    {
        if ($amount instanceof self) {
            $amount = $amount->value;
        }

        return $this->value <= $amount;
    }

    final public function lt(self|float|int $amount): bool
    {
        if ($amount instanceof self) {
            $amount = $amount->value;
        }

        return $this->value < $amount;
    }

    final public function stringify(): string
    {
        return number_format($this->value, 2, '.', '');
    }
}

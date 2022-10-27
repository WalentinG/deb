<?php

declare(strict_types=1);

namespace support\marshal;

final class ParameterNotFound extends \InvalidArgumentException
{
    public function __construct(
        private readonly string $class,
        private readonly string $parameter
    ) {
        parent::__construct("Parameter {$this->parameter} not found to unmarshal {$this->class}");
    }

    /** @return array<string, string> */
    public function context(): array
    {
        return [
            'class' => $this->class,
            'parameter' => $this->parameter,
        ];
    }
}

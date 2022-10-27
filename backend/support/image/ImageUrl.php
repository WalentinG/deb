<?php

declare(strict_types=1);

namespace support\image;

use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

final class ImageUrl
{
    public function __construct(public string $value)
    {
        try {
            v::stringType()->check($this->value);
        } catch (ValidationException $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}

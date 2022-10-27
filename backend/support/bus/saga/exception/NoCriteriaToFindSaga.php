<?php

declare(strict_types=1);

namespace support\bus\saga\exception;

final class NoCriteriaToFindSaga extends \UnexpectedValueException
{
    public static function for(object $message): self
    {
        return new self('No configuration found to load saga by ' . $message::class);
    }
}

<?php

declare(strict_types=1);

namespace support\bus\saga\handler;

use support\bus\BusContext;
use support\bus\MessageHandler;
use support\bus\saga\exception\SagaNotFound;

final class UpdateOrCreateSagaHandler implements MessageHandler
{
    public function __construct(
        private readonly UpdateSagaHandler $updateSagaHandler,
        private readonly CreateSagaHandler $createSagaHandler,
    ) {
    }

    public function id(): string
    {
        return $this->updateSagaHandler->id();
    }

    public function __invoke(object $message, BusContext $x): void
    {
        try {
            ($this->updateSagaHandler)($message, $x);
        } catch (SagaNotFound) {
            ($this->createSagaHandler)($message, $x);
        }
    }
}

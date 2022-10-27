<?php

declare(strict_types=1);

namespace support\bus\saga\handler;

use support\bus\BusContext;
use support\bus\MessageHandler;
use support\bus\saga\attribute\SagaHeader;
use support\bus\saga\Saga;
use support\bus\saga\Sagas;

use function support\bus\saga\release;

final class CreateSagaHandler implements MessageHandler
{
    /** @param class-string<Saga> $sagaClass */
    public function __construct(
        private readonly Sagas $sagas,
        private readonly string $sagaClass,
        private readonly string $method,
        private readonly SagaHeader $header
    ) {
    }

    public function id(): string
    {
        return $this->sagaClass . $this->method;
    }

    public function __invoke(object $message, BusContext $x): void
    {
        /** @var \Generator<int, object, null, Saga>|Saga */
        $result = $this->sagaClass::{$this->method}($message);

        $messages = release($result instanceof \Generator ? $result : []);

        $saga = $result instanceof Saga ? $result : $result->getReturn();

        $this->sagas->create($saga, $this->header->table);

        $x->deliveryBulk($messages);
    }
}

<?php

declare(strict_types=1);

namespace support\bus\saga\handler;

use support\bus\BusContext;
use support\bus\MessageHandler;
use support\bus\mutex\MutexService;
use support\bus\saga\attribute\SagaHeader;
use support\bus\saga\exception\NoCriteriaToFindSaga;
use support\bus\saga\Saga;
use support\bus\saga\Sagas;

use function support\bus\saga\release;

final class UpdateSagaHandler implements MessageHandler
{
    /** @param class-string<Saga> $sagaClass */
    public function __construct(
        private readonly Sagas $sagas,
        private readonly string $sagaClass,
        private readonly string $method,
        private readonly SagaHeader $header,
        private readonly MutexService $mutex
    ) {
    }

    public function id(): string
    {
        return $this->sagaClass . $this->method;
    }

    public function __invoke(object $message, BusContext $x): void
    {
        /** @var Saga $sagaClass */
        $sagaClass = $this->sagaClass;

        $criteria = $sagaClass::criteriaToFindSaga($message);

        if (empty($criteria)) {
            throw NoCriteriaToFindSaga::for($message);
        }

        if (array_keys($criteria) !== $this->header->id) {
            $criteria = $this->sagas->id($this->header->id, $this->sagaClass, $this->header->table, $criteria);
        }

        $messages = $this->mutex->withLock($this->header->sagaId($criteria), function () use ($criteria, $message) {
            $saga = $this->sagas->get($this->sagaClass, $this->header->table, $criteria);
            /** @var array<object> $messages */
            $messages = release($saga->{$this->method}($message));

            $this->sagas->update($saga, $this->header->table, $criteria);

            return $messages;
        });

        $x->deliveryBulk($messages);
    }
}

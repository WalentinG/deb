<?php

declare(strict_types=1);

namespace support\bus\entrypoint;

use support\bus\ContextFactory;
use support\bus\messagesRouter\Router;
use support\bus\transport\IncomingPackage;

final class EntrypointProcessor
{
    public function __construct(
        private readonly Decoders $decoders,
        private readonly ContextFactory $contextFactory,
        private readonly Router $messageRouter,
        private readonly ?RetryStrategy $retryStrategy = null,
    ) {
    }

    public function process(IncomingPackage $package): void
    {
        $x = $this->contextFactory->create($package);
        $message = $this->decoders->get($package->metadata->contentType())->decode($package->payload, $package->metadata->messageType());

        foreach ($this->messageRouter->match($message) as $executor) {
            try {
                $executor($message, $x);
            } catch (\Throwable $throwable) {
                if (!$this->retryStrategy) {
                    throw $throwable;
                }
                $this->retryStrategy->retry($message, $x, $executor);
            }
        }
    }
}

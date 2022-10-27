<?php

declare(strict_types=1);

namespace support\bus\messagesRouter;

use support\bus\MessageHandler;
use support\bus\messagesRouter\exception\InvalidCommandClassSpecified;
use support\bus\messagesRouter\exception\InvalidEventClassSpecified;
use support\bus\messagesRouter\exception\MultipleCommandHandlersNotAllowed;

final class Router
{
    /** @var MessageHandler[][] */
    private array $listeners = [];
    /** @var MessageHandler[] */
    private array $handlers = [];

    public static function configure(Configurator ...$configurators): self
    {
        $router = new self();
        foreach ($configurators as $configurator) {
            $configurator->configure($router);
        }

        return $router;
    }

    /** @return MessageHandler[] */
    public function match(object $message): array
    {
        $messageClass = $message::class;

        if (isset($this->listeners[$messageClass])) {
            return $this->listeners[$messageClass];
        }

        if (isset($this->handlers[$messageClass])) {
            return [$this->handlers[$messageClass]];
        }

        return [];
    }

    public function registerListener(object|string $event, MessageHandler $handler): void
    {
        $eventClass = \is_object($event) ? $event::class : $event;

        if (false === class_exists($eventClass)) {
            throw InvalidEventClassSpecified::wrongEventClass();
        }

        $this->listeners[$eventClass][] = $handler;
    }

    public function registerHandler(object|string $command, MessageHandler $handler): void
    {
        $commandClass = \is_object($command) ? $command::class : $command;

        if (false === class_exists($commandClass)) {
            throw InvalidCommandClassSpecified::wrongCommandClass();
        }

        if (isset($this->handlers[$commandClass])) {
            throw MultipleCommandHandlersNotAllowed::duplicate($commandClass);
        }

        $this->handlers[$commandClass] = $handler;
    }
}

<?php

declare(strict_types=1);

namespace support\bus\serviceHandler;

use Psr\Container\ContainerInterface;
use support\bus\annotationsReader\attribute\MethodLevel;
use support\bus\annotationsReader\AttributesReader;
use support\bus\serviceHandler\attribute\CommandHandler;
use support\bus\serviceHandler\attribute\EventHandler;
use support\bus\serviceHandler\handler\InstanceMessageHandler;
use support\bus\serviceHandler\handler\StaticMessageHandler;
use support\bus\MessageHandler;
use support\bus\messagesRouter\Configurator;
use support\bus\messagesRouter\Router;

use function support\bus\extractMessageClasses;

final class RouterConfigurator implements Configurator
{
    /** @param class-string[] $classList */
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly array $classList,
        private readonly AttributesReader $attributesReader = new AttributesReader()
    ) {
    }

    public function configure(Router $router): void
    {
        foreach ($this->classList as $className) {
            $this->registerHandlers($className, $router);
        }
    }

    /** @param class-string $className */
    private function registerHandlers(string $className, Router $router): void
    {
        $attributes = $this->attributesReader->extract($className);

        $eventHandlerMethods = $this->extractEventHandlerMethods($attributes->methodLevelCollection);
        $commandHandlerMethods = $this->extractCommandHandlerMethods($attributes->methodLevelCollection);

        foreach ($eventHandlerMethods as $eventClass => $methods) {
            foreach ($methods as $method) {
                $router->registerListener($eventClass, $this->createMessageHandler($className, $method));
            }
        }

        foreach ($commandHandlerMethods as $commandClass => $methods) {
            foreach ($methods as $method) {
                $router->registerHandler($commandClass, $this->createMessageHandler($className, $method));
            }
        }
    }

    /**
     * @psalm-param class-string $className
     */
    private function createMessageHandler(string $className, \ReflectionMethod $method): MessageHandler
    {
        if ($method->isStatic()) {
            return new StaticMessageHandler($className, $method->name);
        }

        return new InstanceMessageHandler($this->container, $className, $method->name);
    }

    /**
     * @psalm-param \SplObjectStorage<MethodLevel, int> $methodLevelAttributes
     *
     * @return array<class-string, array<\ReflectionMethod>>
     */
    private function extractEventHandlerMethods(\SplObjectStorage $methodLevelAttributes): array
    {
        $methods = [];

        foreach ($methodLevelAttributes as $methodLevelAttribute) {
            if ($methodLevelAttribute->attribute instanceof EventHandler) {
                $reflectionMethod = $methodLevelAttribute->reflectionMethod;
                foreach (extractMessageClasses($reflectionMethod) as $messageClass) {
                    $methods[$messageClass][] = $reflectionMethod;
                }
            }
        }

        return $methods;
    }

    /**
     * @psalm-param \SplObjectStorage<MethodLevel, int> $methodLevelAttributes
     *
     * @return array<class-string, array<\ReflectionMethod>>
     */
    private function extractCommandHandlerMethods(\SplObjectStorage $methodLevelAttributes): array
    {
        $commands = [];

        foreach ($methodLevelAttributes as $methodLevelAttribute) {
            if ($methodLevelAttribute->attribute instanceof CommandHandler) {
                $reflectionMethod = $methodLevelAttribute->reflectionMethod;
                foreach (extractMessageClasses($reflectionMethod) as $messageClass) {
                    $commands[$messageClass][] = $reflectionMethod;
                }
            }
        }

        return $commands;
    }
}

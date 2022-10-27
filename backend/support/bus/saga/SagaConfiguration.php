<?php

declare(strict_types=1);

namespace support\bus\saga;

use support\bus\annotationsReader\attribute\ClassLevel;
use support\bus\annotationsReader\attribute\MethodLevel;
use support\bus\annotationsReader\AttributesReader;
use support\bus\messagesRouter\Configurator;
use support\bus\messagesRouter\Router;
use support\bus\mutex\MutexService;
use support\bus\saga\attribute\SagaCommandHandler;
use support\bus\saga\attribute\SagaEventHandler;
use support\bus\saga\attribute\SagaHeader;
use support\bus\saga\handler\CreateSagaHandler;
use support\bus\saga\handler\UpdateOrCreateSagaHandler;
use support\bus\saga\handler\UpdateSagaHandler;

use function support\bus\extractMessageClasses;

final class SagaConfiguration implements Configurator
{
    /** @param class-string<Saga>[] $classList */
    public function __construct(
        private readonly Sagas $sagas,
        private readonly MutexService $mutex,
        private readonly array $classList,
        private readonly AttributesReader $attributesReader = new AttributesReader(),
    ) {
    }

    public function configure(Router $router): void
    {
        foreach ($this->classList as $className) {
            $this->registerHandlers($className, $router);
        }
    }

    /** @param class-string<Saga> $sagaClass */
    public function registerHandlers(string $sagaClass, Router $router): void
    {
        $attributes = $this->attributesReader->extract($sagaClass);

        $sagaHeader = self::searchSagaHeader($sagaClass, $attributes->classLevelCollection);

        $eventHandlerMethods = $this->extractEventHandlerMethods($attributes->methodLevelCollection);
        $commandHandlerMethods = $this->extractCommandHandlerMethods($attributes->methodLevelCollection);

        foreach ($eventHandlerMethods as $eventClass => $methods) {
            $router->registerListener($eventClass, $this->createMessageHandler($this->sagas, $sagaClass, $methods, $sagaHeader));
        }

        foreach ($commandHandlerMethods as $commandClass => $methods) {
            $router->registerListener($commandClass, $this->createMessageHandler($this->sagas, $sagaClass, $methods, $sagaHeader));
        }
    }

    /**
     * @param class-string<Saga>       $sagaClass
     * @param array<\ReflectionMethod> $methods
     */
    private function createMessageHandler(Sagas $sagas, string $sagaClass, array $methods, SagaHeader $header): CreateSagaHandler|UpdateSagaHandler|UpdateOrCreateSagaHandler
    {
        $createSagaMethods = array_values(array_filter($methods, fn (\ReflectionMethod $m) => $m->isStatic()));
        $updateSagaMethods = array_values(array_filter($methods, fn (\ReflectionMethod $m) => !$m->isStatic()));

        if (\count($createSagaMethods) > 1 || \count($updateSagaMethods) > 1) {
            throw new \LogicException('Can\'n be more than one method handler per class');
        }

        if (isset($createSagaMethods[0], $updateSagaMethods[0])) {
            return new UpdateOrCreateSagaHandler(
                new UpdateSagaHandler($sagas, $sagaClass, $updateSagaMethods[0]->name, $header, $this->mutex),
                new CreateSagaHandler($sagas, $sagaClass, $createSagaMethods[0]->name, $header)
            );
        }

        if (isset($createSagaMethods[0])) {
            return new CreateSagaHandler($sagas, $sagaClass, $createSagaMethods[0]->name, $header);
        }

        if (isset($updateSagaMethods[0])) {
            return new UpdateSagaHandler($sagas, $sagaClass, $updateSagaMethods[0]->name, $header, $this->mutex);
        }

        throw new \LogicException('No method found to handle message');
    }

    /**
     * @param \SplObjectStorage<MethodLevel, int> $methodLevelAttributes
     *
     * @return array<class-string, array<\ReflectionMethod>>
     */
    private function extractEventHandlerMethods(\SplObjectStorage $methodLevelAttributes): array
    {
        $methods = [];

        foreach ($methodLevelAttributes as $methodLevelAttribute) {
            if ($methodLevelAttribute->attribute instanceof SagaEventHandler) {
                $reflectionMethod = $methodLevelAttribute->reflectionMethod;
                foreach (extractMessageClasses($reflectionMethod) as $messageClass) {
                    $methods[$messageClass][] = $reflectionMethod;
                }
            }
        }

        return $methods;
    }

    /**
     * @param \SplObjectStorage<MethodLevel, int> $methodLevelAttributes
     *
     * @return array<class-string, array<\ReflectionMethod>>
     */
    private function extractCommandHandlerMethods(\SplObjectStorage $methodLevelAttributes): array
    {
        $commands = [];

        foreach ($methodLevelAttributes as $methodLevelAttribute) {
            if ($methodLevelAttribute->attribute instanceof SagaCommandHandler) {
                $reflectionMethod = $methodLevelAttribute->reflectionMethod;
                foreach (extractMessageClasses($reflectionMethod) as $messageClass) {
                    $commands[$messageClass][] = $reflectionMethod;
                }
            }
        }

        return $commands;
    }

    /**
     * @param \SplObjectStorage<ClassLevel, int> $classLevelAttributes
     * @param class-string<Saga>                 $sagaClass
     */
    private static function searchSagaHeader(string $sagaClass, \SplObjectStorage $classLevelAttributes): SagaHeader
    {
        foreach ($classLevelAttributes as $attributes) {
            $attributeObject = $attributes->attribute;

            if ($attributeObject instanceof SagaHeader) {
                return $attributeObject;
            }
        }

        throw new \InvalidArgumentException(sprintf('Could not find class-level attributes "%s" in "%s"', SagaHeader::class, $sagaClass));
    }
}
